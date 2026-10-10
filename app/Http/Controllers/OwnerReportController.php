<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Due;
use App\Models\OwnerReportLink;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OwnerReportController extends Controller
{
    public function show(Request $request, string $token): View
    {
        $link = OwnerReportLink::query()
            ->with(['apartment.buildingBlock.site', 'apartment.activeResident'])
            ->where('token', $token)
            ->firstOrFail();

        if ($link->is_expired) {
            abort(410, 'Bu rapor bağlantısının süresi dolmuş.');
        }

        $link->update(['last_used_at' => now()]);

        $year = (int) $request->query('year', now()->year);
        $report = $this->apartmentReport($link->apartment, $year);

        $money = function ($amount) {
            return number_format((float) $amount, 2, ',', '.').' ₺';
        };

        return view('owner-reports.show', compact('link', 'year', 'report', 'money'));
    }

    public function exportPdf(Request $request, string $token): Response
    {
        $link = OwnerReportLink::query()
            ->with(['apartment.buildingBlock.site', 'apartment.activeResident'])
            ->where('token', $token)
            ->firstOrFail();

        $year = (int) $request->query('year', now()->year);
        $report = $this->apartmentReport($link->apartment, $year);

        $filename = "Daire-{$link->apartment->number}-Aidat-Raporu-{$year}.pdf";

        return $this->generatePdfResponse('owner-reports.pdf', compact('link', 'year', 'report'), $filename);
    }

    public function exportPdfByApartment(Request $request, Apartment $apartment): Response
    {
        $apartment->load(['buildingBlock.site', 'activeResident']);
        $year = (int) $request->query('year', now()->year);
        $report = $this->apartmentReport($apartment, $year);

        $link = null;
        $filename = "Daire-{$apartment->number}-Aidat-Raporu-{$year}.pdf";

        return $this->generatePdfResponse('owner-reports.pdf', compact('apartment', 'year', 'report', 'link'), $filename);
    }

    public function showByApartment(Request $request, Apartment $apartment): View
    {
        $apartment->load(['buildingBlock.site', 'activeResident']);

        $year = (int) $request->query('year', now()->year);
        $report = $this->apartmentReport($apartment, $year);

        $money = function ($amount) {
            return number_format((float) $amount, 2, ',', '.').' ₺';
        };

        $link = (object) [
            'apartment' => $apartment,
            'apartment_id' => $apartment->id,
            'is_expired' => false,
            'expires_at' => null,
            'created_at' => now(),
            'last_used_at' => now(),
        ];

        return view('owner-reports.show', compact('apartment', 'year', 'report', 'money', 'link'));
    }

    private function generatePdfResponse(string $view, array $data, string $filename): Response
    {
        $options = new Options;
        $options->set('defaultFont', 'DejaVu Serif');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $html = view($view, $data)->render();

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function apartmentReport(Apartment $apartment, int $year): array
    {
        $dues = Due::query()
            ->with('payments.bankTransaction')
            ->where('apartment_id', $apartment->id)
            ->where('period_year', $year)
            ->orderBy('period_month')
            ->get()
            ->keyBy('period_month');

        $months = [];
        $monthlyBreakdown = [];
        $totals = ['due' => 0.0, 'paid' => 0.0, 'pending' => 0.0];

        foreach ($this->months() as $number => $name) {
            $due = $dues->get($number);
            $amount = $due ? (float) $due->amount : 0.0;
            $paid = $due ? (float) $due->payments->sum('amount') : 0.0;
            $pending = max(0, $amount - $paid);

            $statusText = $amount === 0.0 ? 'Kayıt yok' : ($pending <= 0 ? 'Ödendi' : ($paid > 0 ? 'Kısmi' : 'Bekliyor'));

            $monthData = [
                'name' => $name,
                'due' => $amount,
                'paid' => $paid,
                'pending' => $pending,
                'status' => $statusText,
                'source' => $paid > 0 ? $due?->paymentSourceLabel() : null,
                'source_class' => $paid > 0 ? $due?->paymentSourceBadgeClass() : 'bg-secondary-lt',
                'rate' => $amount > 0 ? min(100, (int) round(($paid / $amount) * 100)) : 0,
            ];

            $months[$number] = $monthData;

            $monthlyBreakdown[] = array_merge($monthData, [
                'month_name' => $name,
                'month' => $number,
                'year' => $year,
                'remaining' => $pending,
                'status_code' => $pending <= 0 && $amount > 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending'),
            ]);

            $totals['due'] += $amount;
            $totals['paid'] += $paid;
            $totals['pending'] += $pending;
        }

        return [
            'apartment' => $apartment,
            'site' => $apartment->buildingBlock->site ?? null,
            'year' => $year,
            'months' => $months,
            'monthly_breakdown' => $monthlyBreakdown,
            'totals' => $totals,
            'collection_rate' => $totals['due'] > 0 ? min(100, (int) round(($totals['paid'] / $totals['due']) * 100)) : 0,
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
