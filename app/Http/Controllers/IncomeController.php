<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Income;
use App\Models\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function index(Request $request): View
    {
        $sites = Site::query()->orderBy('name')->get();
        $site = Site::query()->find($request->query('site_id')) ?? $sites->firstOrFail();
        $year = (int) request('year', now()->year);
        $monthInput = $request->query('month', (string) now()->month);
        $month = $monthInput === '' ? null : max(1, min(12, (int) $monthInput));
        $category = (string) $request->query('category', '');
        $search = trim((string) $request->query('search', ''));

        $incomes = Income::query()
            ->where('site_id', $site->id)
            ->whereYear('income_date', $year)
            ->when($month, fn ($query) => $query->whereMonth('income_date', $month))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('category', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest('income_date')
            ->paginate(15)
            ->withQueryString();

        return view('incomes.index', [
            'site' => $site,
            'sites' => $sites,
            'incomes' => $incomes,
            'months' => $this->months(),
            'categories' => $this->categories(),
            'year' => $year,
            'month' => $month,
            'category' => $category,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('incomes.create', [
            'site' => Site::query()->firstOrFail(),
            'categories' => $this->categories(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'income_date' => ['required', 'date'],
        ]);

        $site = Site::query()->firstOrFail();
        $income = Income::create($validated + ['site_id' => $site->id]);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_income',
            'table_name' => 'incomes',
            'record_id' => $income->id,
            'ip_address' => $request->ip(),
            'description' => "{$income->category} geliri kaydedildi.",
        ]);

        return redirect()->route('incomes.index')->with('status', 'Gelir kaydedildi.');
    }

    public function edit(Income $income): View
    {
        return view('incomes.edit', [
            'income' => $income,
            'categories' => $this->categories(),
        ]);
    }

    public function update(Request $request, Income $income): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'income_date' => ['required', 'date'],
        ]);

        $income->update($validated);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_income',
            'table_name' => 'incomes',
            'record_id' => $income->id,
            'ip_address' => $request->ip(),
            'description' => "{$income->category} geliri güncellendi.",
        ]);

        return redirect()->route('incomes.index')->with('status', 'Gelir güncellendi.');
    }

    public function destroy(Request $request, Income $income): RedirectResponse
    {
        $description = $income->description;
        $income->delete();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_income',
            'table_name' => 'incomes',
            'ip_address' => $request->ip(),
            'description' => "{$description} geliri silindi.",
        ]);

        return redirect()->route('incomes.index')->with('status', 'Gelir silindi.');
    }

    private function categories(): array
    {
        return ['Aidat Dışı Tahsilat', 'Kısmi Aidat Tahsilatı', 'Diğer'];
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
