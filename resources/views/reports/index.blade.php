@extends('layouts.app', ['title' => 'Raporlar - Site360Pro'])

@php
    $money = fn ($value) => number_format($value, 2, ',', '.') . ' ₺';
@endphp

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-3 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Finansal Analiz</div>
                    <h2 class="page-title">Raporlar</h2>
                    <div class="text-secondary mt-1">{{ $site->name }} için {{ $periodLabel }} tahakkuk, tahsilat, gelir, gider ve bakiye raporu.</div>
                </div>
                <div class="col-auto">
                    <a class="btn btn-primary" href="{{ route('reports.pdf', ['site_id' => $site->id, 'year' => $year, 'month' => $month]) }}">
                        <x-icon name="file-type-pdf" class="me-1" /> PDF İndir
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card mb-3">
                <div class="card-body">
                    <form class="d-flex justify-content-between align-items-end w-100" method="get" action="{{ route('reports.index') }}">
                        <div class="col-7 col-md-7 col-lg-7">
                            <label class="form-label">Site</label>
                            <select class="form-select" name="site_id">
                                @foreach ($sites as $filterSite)
                                    <option value="{{ $filterSite->id }}" @selected($filterSite->is($site))>{{ $filterSite->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-1 col-md-1 col-lg-1">
                            <label class="form-label">Yıl</label>
                            <input class="form-control text-center" type="number" name="year" value="{{ $year }}" min="2020" max="2100">
                        </div>
                        <div class="col-2 col-md-2 col-lg-2">
                            <label class="form-label">Ay</label>
                            <select class="form-select" name="month">
                                <option value="">Tüm yıl</option>
                                @foreach ($months as $number => $name)
                                    <option value="{{ $number }}" @selected($month === $number)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" type="submit"><x-icon name="filter"/> Raporu Getir</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row row-cards mb-3">
                <div class="col-sm-6 col-md-4">
                        <div class="card metric-card h-100" style="min-height: 0;">
                            <div class="card-body text-center position-relative" style="padding: .7rem 1rem;">
                                <div class="d-flex justify-content-center align-items-center">
                                    <span class="text-secondary fw-semibold" style="color: #14b8a6 !important;">Kasa Devri</span>
                                    <span class="avatar position-absolute bg-teal-lt" style="top: .6rem; right: .75rem; width: 2rem; height: 2rem; font-size: 1rem; margin: 0 !important;"><x-icon name="building-bank" class="" /></span>
                                </div>
                                <div class="h1 mt-1 mb-0" style="font-size: 1.5rem; color: #14b8a6 !important; -webkit-text-fill-color: #14b8a6 !important; background: none !important;">{{ $money($report['totals']['carryover']) }}</div>
                                <div class="text-secondary small fw-semibold" style="color: #14b8a6 !important;">{{ $month ? 'Önceki aydan devreden kasa' : 'Yıl başı kasa devri' }}</div>
                            </div>
                        </div>
                </div>
                <div class="col-sm-6 col-md-4">
                        <div class="card metric-card h-100" style="min-height: 0;">
                        <div class="card-body text-center position-relative" style="padding: .7rem 1rem;">
                            <div class="d-flex justify-content-center align-items-center">
                                <span class="text-secondary fw-semibold" style="color: deepskyblue !important;">{{ $month ? 'Dönem Tahakkuku' : 'Yıllık Tahakkuk' }}</span>
                                <span class="avatar position-absolute bg-deepskyblue-lt" style="top: .6rem; right: .75rem; width: 2rem; height: 2rem; font-size: 1rem; margin: 0 !important; color: deepskyblue !important;"><x-icon name="receipt" class="" /></span>
                            </div>
                            <div class="h1 mt-1 mb-0" style="font-size: 1.5rem; color: deepskyblue !important; -webkit-text-fill-color: deepskyblue !important; background: none !important;">{{ $money($report['totals']['due']) }}</div>
                            <div class="text-secondary small fw-semibold" style="color: deepskyblue !important;">Toplam borçlandırma</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                        <div class="card metric-card h-100" style="min-height: 0;">
                        <div class="card-body text-center position-relative" style="padding: .7rem 1rem;">
                            <div class="d-flex justify-content-center align-items-center">
                                <span class="text-secondary fw-semibold" style="color: orange !important;">{{ $month ? 'Dönem Tahsilatı' : 'Yıllık Tahsilat' }}</span>
                                <span class="avatar position-absolute bg-orange-lt" style="top: .6rem; right: .75rem; width: 2rem; height: 2rem; font-size: 1rem; margin: 0 !important;"><x-icon name="receipt" class="" /></span>
                            </div>
                            <div class="h1 mt-1 mb-0" style="font-size: 1.5rem; color: orange !important; -webkit-text-fill-color: orange !important; background: none !important;">{{ $money($report['totals']['paid']) }}</div>
                            <div class="text-secondary small fw-semibold" style="color: orange !important;">%{{ $report['collection_rate'] }} tahsilat</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                        <div class="card metric-card h-100" style="min-height: 0;">
                        <div class="card-body text-center position-relative" style="padding: .7rem 1rem;">
                            <div class="d-flex justify-content-center align-items-center">
                                <span class="text-secondary fw-semibold" style="color: #14b8a6 !important;">{{ $month ? 'Dönem Gelirler Toplamı' : 'Yıllık Gelirler' }}</span>
                                <span class="avatar position-absolute bg-teal-lt" style="top: .6rem; right: .75rem; width: 2rem; height: 2rem; font-size: 1rem; margin: 0 !important;"><x-icon name="cash-banknote" class="" /></span>
                            </div>
                            <div class="h1 mt-1 mb-0" style="font-size: 1.5rem; color: #14b8a6 !important; -webkit-text-fill-color: #14b8a6 !important; background: none !important;">{{ $money($report['totals']['income_total']) }}</div>
                            <div class="text-secondary small fw-semibold" style="color: #14b8a6 !important;">Kayıtlı gelirler toplamı</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                        <div class="card metric-card h-100" style="min-height: 0;">
                        <div class="card-body text-center position-relative" style="padding: .7rem 1rem;">
                            <div class="d-flex justify-content-center align-items-center">
                                <span class="text-secondary fw-semibold" style="color: red !important;">{{ $month ? 'Dönem Giderler Toplamı' : 'Yıllık Giderler' }}</span>
                                <span class="avatar position-absolute bg-red-lt" style="top: .6rem; right: .75rem; width: 2rem; height: 2rem; font-size: 1rem; margin: 0 !important;"><x-icon name="circle-check" class="" /></span>
                            </div>
                            <div class="h1 mt-1 mb-0" style="font-size: 1.5rem; color: red !important; -webkit-text-fill-color: red !important; background: none !important;">{{ $money($report['totals']['expense']) }}</div>
                            <div class="text-secondary small fw-semibold" style="color: red !important;">Kayıtlı giderler toplamı</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                        <div class="card metric-card h-100" style="min-height: 0;">
                        <div class="card-body text-center position-relative" style="padding: .7rem 1rem;">
                            <div class="d-flex justify-content-center align-items-center">
                                <span class="text-secondary fw-semibold" style="color: green !important;">{{ $month ? 'Dönem Sonu Kasa' : 'Yıl Sonu Kasa' }}</span>
                                <span class="avatar position-absolute bg-green-lt" style="top: .6rem; right: .75rem; width: 2rem; height: 2rem; font-size: 1rem; margin: 0 !important;"><x-icon name="building-bank" class="" /></span>
                            </div>
                            <div class="h1 mt-1 mb-0" style="font-size: 1.5rem; color: green !important; -webkit-text-fill-color: green !important; background: none !important;">{{ $money($report['totals']['balance']) }}</div>
                            <div class="text-secondary small fw-semibold" style="color: green !important;">Gelirler - Giderler</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-cards">
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Aylık Grafik</h3></div>
                        <div class="card-body">
                            @php

                                $currentPeriod = now()->year * 12 + now()->month;
                            @endphp
                            <div class="report-chart">
                                @foreach ($report['months'] as $month)
                                    <div class="report-chart-row">
                                        <div class="report-chart-label">{{ $month['name'] }}</div>
                                        <div class="report-chart-track">
                                            <span class="report-bar report-bar-income" style="width: {{ ($year * 12 + $month['number']) <= $currentPeriod && $month['income_total'] > 0 ? max(2, ($month['income_total'] / $report['max']) * 100) : 0 }}%; background: #14b8a6 !important;"></span>
                                            <span class="report-bar report-bar-expense" style="width: {{ ($year * 12 + $month['number']) <= $currentPeriod && $month['expense'] > 0 ? max(2, ($month['expense'] / $report['max']) * 100) : 0 }}%; background: #ef4444 !important;"></span>
                                            <span class="report-bar report-bar-balance" style="width: {{ ($year * 12 + $month['number']) <= $currentPeriod && $month['balance'] > 0 ? max(2, ($month['balance'] / $report['max']) * 100) : 0 }}%; background: #22c55e !important;"></span>
                                        </div>
                                        <div class="report-chart-value">%{{ $month['rate'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex gap-3 mt-3 text-secondary small">
                                <span><span class="legend-dot" style="background: #14b8a6;"></span> Gelirler</span>
                                <span><span class="legend-dot bg-danger"></span> Giderler</span>
                                <span><span class="legend-dot" style="background: #22c55e;"></span> Kasa Bakiyesi</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Yıl Özeti</h3></div>
                        <div class="card-body text-center">
                            <div class="collection-ring dashboard-ring" style="--rate: {{ $report['collection_rate'] }}%;">
                                <div class="collection-ring-inner">
                                    <div><strong>%{{ $report['collection_rate'] }}</strong><span class="text-secondary">Tahsilat</span></div>
                                </div>
                            </div>
                            <div class="text-secondary mt-3">{{ $periodLabel }} için kalan borç {{ $money($report['totals']['pending']) }}.</div>
                            <div class="list-group list-group-flush mt-3 text-start">
                                @foreach ($report['totals']['sources'] as $source)
                                    <div class="list-group-item px-0 d-flex justify-content-between">
                                        <span class="text-secondary">{{ $source['label'] }}</span>
                                        <strong>{{ $money($source['amount']) }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Aylık Detay</h3></div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Ay</th>
                                        <th>Devir</th>
                                        <th>Tahakkuk</th>
                                        <th>Tahsilat</th>
                                        <th>Gelir</th>
                                        <th>Gelir Toplamı</th>
                                        <th>Gider</th>
                                        <th>Kalan Borç</th>
                                        <th>Kasa</th>
                                        <th>Oran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($report['months'] as $month)
                                        <tr>
                                            <td class="fw-bold">{{ $month['name'] }}</td>
                                            <td style="color: #14b8a6;">{{ $money($month['carryover']) }}</td>
                                            <td class="text-primary">{{ $money($month['due']) }}</td>
                                            <td class="text-warning">{{ $money($month['paid']) }}</td>
                                            <td style="color: #14b8a6;">{{ $money($month['income']) }}</td>
                                            <td style="color: #14b8a6;">{{ $money($month['carryover'] + $month['paid'] + $month['income']) }}</td>
                                            <td class="text-danger">{{ $money($month['expense']) }}</td>
                                            <td class="text-danger">{{ $money($month['pending']) }}</td>
                                            <td class="{{ $month['balance'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $money($month['balance']) }}</td>
                                            <td><span style="color: var(--text-primary);">%{{ $month['rate'] }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
