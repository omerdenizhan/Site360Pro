<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Payment;
use App\Models\Site;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $sites = Site::query()->orderBy('name')->get();
        $site = Site::query()->find($request->query('site_id')) ?? $sites->firstOrFail();
        $year = (int) $request->query('year', now()->year);
        $month = $request->filled('month')
            ? max(1, min(12, (int) $request->query('month')))
            : null;
        $months = $this->months();
        $report = $this->filterReportByMonth(
            $this->siteReport($site, $year),
            $month
        );
        $periodLabel = $month
            ? $months[$month].' '.$year
            : $year.' yılı';

        return view(
            'reports.index',
            compact(
                'sites',
                'site',
                'year',
                'month',
                'months',
                'report',
                'periodLabel'
            )
        );
    }

    public function pdf(Request $request): Response
    {
        $site = Site::query()->with('buildingBlocks')->findOrFail($request->query('site_id'));

        $year = (int) $request->query('year', now()->year);

        $month = $request->filled('month')
            ? max(1, min(12, (int) $request->query('month')))
            : null;

        $months = $this->months();

        $report = $this->filterReportByMonth(
            $this->siteReport($site, $year),
            $month
        );

        $periodLabel = $month
            ? $months[$month].' '.$year
            : $year.' yılı';

        $blockName = $site->buildingBlocks->pluck('name')->filter()->join(' - ');
        if (empty($blockName)) {
            $blockName = $site->buildingBlocks->first()?->name ?? 'Blok';
        }

        $html = view(
            'reports.pdf',
            compact(
                'site',
                'blockName',
                'year',
                'month',
                'months',
                'periodLabel',
                'report'
            )
        )->render();

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Serif');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rapor-'
                .$site->id
                .'-'
                .$year
                .($month ? '-'.$month : '')
                .'.pdf"',
        ]);
    }

    private function siteReport(Site $site, int $year): array
    {
        $dueRows = DB::table('dues')
            ->join(
                'apartments',
                'apartments.id',
                '=',
                'dues.apartment_id'
            )
            ->join(
                'building_blocks',
                'building_blocks.id',
                '=',
                'apartments.building_block_id'
            )
            ->where('building_blocks.site_id', $site->id)
            ->where('dues.period_year', $year)
            ->groupBy('dues.period_month')
            ->selectRaw(
                'dues.period_month as month,
                 SUM(dues.amount) as amount,
                 COUNT(*) as count'
            )
            ->get()
            ->keyBy('month');

        $paymentRows = Payment::query()
            ->whereYear('paid_at', $year)
            ->whereHas(
                'due.apartment.buildingBlock',
                fn ($query) => $query->where('site_id', $site->id)
            )
            ->selectRaw(
                'MONTH(paid_at) as month,
                 SUM(amount) as amount,
                 COUNT(*) as count'
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $expenseRows = Expense::query()
            ->where('site_id', $site->id)
            ->whereYear('expense_date', $year)
            ->selectRaw(
                'MONTH(expense_date) as month,
                 SUM(amount) as amount,
                 COUNT(*) as count'
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $incomeRows = Income::query()
            ->where('site_id', $site->id)
            ->whereYear('income_date', $year)
            ->selectRaw(
                'MONTH(income_date) as month,
                 SUM(amount) as amount,
                 COUNT(*) as count'
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $paymentSourceRows = Payment::query()
            ->leftJoin(
                'bank_transactions',
                'bank_transactions.matched_payment_id',
                '=',
                'payments.id'
            )
            ->whereYear('payments.paid_at', $year)
            ->whereHas(
                'due.apartment.buildingBlock',
                fn ($query) => $query->where('site_id', $site->id)
            )
            ->selectRaw('
                MONTH(payments.paid_at) as month,
                CASE
                    WHEN bank_transactions.status = "matched"
                        THEN "auto"
                    WHEN bank_transactions.status = "manual_matched"
                        THEN "integration_manual"
                    ELSE "panel"
                END as source,
                SUM(payments.amount) as amount,
                COUNT(payments.id) as count
            ')
            ->groupBy('month', 'source')
            ->get()
            ->groupBy('month');

        $cashStart = $site->cashStart();
        $running = $this->cashBefore($site, $year);
        $carrySet = false;

        $months = [];

        $totals = [
            'carryover' => 0.0,
            'income_total' => 0.0,
            'due' => 0.0,
            'paid' => 0.0,
            'income' => 0.0,
            'expense' => 0.0,
            'pending' => 0.0,
            'balance' => 0.0,
            'sources' => $this->emptySourceTotals(),
        ];

        $max = 1.0;

        foreach ($this->months() as $number => $name) {
            $due = (float) (
                $dueRows[$number]->amount ?? 0
            );
            $paid = (float) (
                $paymentRows[$number]->amount ?? 0
            );
            $income = (float) (
                $incomeRows[$number]->amount ?? 0
            );
            $expense = (float) (
                $expenseRows[$number]->amount ?? 0
            );

            $pending = max(0, $due - $paid);
            $monthStart = Carbon::create($year, $number, 1);
            $net = $paid + $income - $expense;

            if ($cashStart && $monthStart->lt($cashStart)) {

                $beforeStart = true;
                $running = 0.0;
                $carryover = 0.0;
                $balance = 0.0;
            } else {
                $beforeStart = false;
                if ($cashStart && $monthStart->equalTo($cashStart)) {
                    $running = (float) $site->opening_balance;
                }
                $carryover = $running;

                $balance = $carryover + $net;
                $running = $balance;
            }

            if (! $beforeStart && ! $carrySet) {
                $totals['carryover'] = $carryover;
                $totals['income_total'] += $carryover;
                $carrySet = true;
            }

            $max = max(
                $max,
                $carryover + $paid + $income,
                $expense,
                $balance
            );

            $sources = $this->emptySourceTotals();

            foreach (
                $paymentSourceRows->get($number, collect()) as $row
            ) {
                $sources[$row->source] = [
                    'label' => $this->sourceLabels()[$row->source],
                    'amount' => (float) $row->amount,
                    'count' => (int) $row->count,
                ];
            }

            $months[$number] = [
                'number' => $number,
                'name' => $name,
                'due' => $due,
                'paid' => $paid,
                'income' => $income,
                'expense' => $expense,
                'pending' => $pending,
                'carryover' => $carryover,
                'income_total' => $beforeStart ? 0.0 : $carryover + $paid + $income,
                'net' => $net,
                'balance' => $balance,

                'rate' => $due > 0
                    ? min(
                        100,
                        (int) round(($paid / $due) * 100)
                    )
                    : 0,
                'due_count' => (int) (
                    $dueRows[$number]->count ?? 0
                ),
                'payment_count' => (int) (
                    $paymentRows[$number]->count ?? 0
                ),
                'income_count' => (int) (
                    $incomeRows[$number]->count ?? 0
                ),
                'expense_count' => (int) (
                    $expenseRows[$number]->count ?? 0
                ),
                'sources' => $sources,
            ];

            $totals['due'] += $due;
            $totals['paid'] += $paid;
            $totals['income'] += $income;
            $totals['expense'] += $expense;
            $totals['pending'] += $pending;
            $totals['balance'] = $balance;
            if (! $beforeStart) {
                $totals['income_total'] += $paid + $income;
            }

            foreach ($sources as $key => $source) {
                $totals['sources'][$key]['amount']
                    += $source['amount'];

                $totals['sources'][$key]['count']
                    += $source['count'];
            }
        }

        return [
            'months' => $months,
            'totals' => $totals,
            'max' => $max,
            'collection_rate' => $totals['due'] > 0
                ? min(
                    100,
                    (int) round(
                        ($totals['paid'] / $totals['due']) * 100
                    )
                )
                : 0,
        ];
    }

    private function cashBefore(Site $site, int $year): float
    {
        $yearStart = Carbon::create($year, 1, 1)->startOfDay();
        $cashStart = $site->cashStart();

        if ($cashStart && $cashStart->gte($yearStart)) {
            return 0.0;
        }

        $payments = (float) Payment::query()
            ->where('paid_at', '<', $yearStart)
            ->when($cashStart, fn ($query) => $query->where('paid_at', '>=', $cashStart))
            ->whereHas(
                'due.apartment.buildingBlock',
                fn ($query) => $query->where('site_id', $site->id)
            )
            ->sum('amount');

        $incomes = (float) Income::query()
            ->where('site_id', $site->id)
            ->whereDate('income_date', '<', $yearStart->toDateString())
            ->when($cashStart, fn ($query) => $query->whereDate('income_date', '>=', $cashStart->toDateString()))
            ->sum('amount');

        $expenses = (float) Expense::query()
            ->where('site_id', $site->id)
            ->whereDate('expense_date', '<', $yearStart->toDateString())
            ->when($cashStart, fn ($query) => $query->whereDate('expense_date', '>=', $cashStart->toDateString()))
            ->sum('amount');

        return ($cashStart ? (float) $site->opening_balance : 0.0) + $payments + $incomes - $expenses;
    }

    private function filterReportByMonth(
        array $report,
        ?int $month
    ): array {
        if (! $month) {
            return $report;
        }

        $monthReport = $report['months'][$month];

        return [
            'months' => [
                $month => $monthReport,
            ],

            'totals' => [
                'due' => $monthReport['due'],
                'paid' => $monthReport['paid'],
                'income' => $monthReport['income'],
                'expense' => $monthReport['expense'],
                'pending' => $monthReport['pending'],
                'carryover' => $monthReport['carryover'],
                'income_total' => $monthReport['income_total'],
                'balance' => $monthReport['balance'],
                'sources' => $monthReport['sources'],
            ],
            'max' => max(
                1.0,
                $monthReport['income_total'],
                $monthReport['expense'],
                $monthReport['balance']
            ),

            'collection_rate' => $monthReport['rate'],
        ];
    }

    private function emptySourceTotals(): array
    {
        return collect($this->sourceLabels())
            ->map(
                fn (string $label) => [
                    'label' => $label,
                    'amount' => 0.0,
                    'count' => 0,
                ]
            )
            ->all();
    }

    private function sourceLabels(): array
    {
        return [
            'auto' => 'Otomatik entegrasyon',
            'integration_manual' => 'Manuel entegrasyon',
            'panel' => 'Panelden işlendi',
        ];
    }

    private function months(): array
    {
        return [
            1 => 'Ocak',
            2 => 'Şubat',
            3 => 'Mart',
            4 => 'Nisan',
            5 => 'Mayıs',
            6 => 'Haziran',
            7 => 'Temmuz',
            8 => 'Ağustos',
            9 => 'Eylül',
            10 => 'Ekim',
            11 => 'Kasım',
            12 => 'Aralık',
        ];
    }
}
