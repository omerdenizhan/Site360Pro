@php
    $money = fn ($value) => number_format($value, 2, ',', '.') . ' ₺';
    $apartment = $link?->apartment ?? $apartment;
    $site = $apartment->buildingBlock->site;
@endphp

<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>Daire No.: {{ $apartment->number }} Raporu - Site360Pro</title>
    <script>
        (function() {
            var theme = localStorage.getItem('SiteAptFinansYonetimi_theme') || '{{ session('theme', 'light') }}';
            document.documentElement.setAttribute('data-bs-theme', theme);
            document.documentElement.classList.add(theme === 'dark' ? 'theme-dark' : 'theme-light');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            padding-bottom: 28px;
        }
        .app-footer-fixed {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background-color: var(--bs-body-bg, #fff);
            border-top: 1px solid var(--bs-border-color-translucent, rgba(0, 0, 0, 0.08));
            padding-top: 10px !important;
            padding-bottom: 10px !important;
            min-height: auto !important;
        }
        .app-footer-fixed span {
            font-size: 0.75rem;
            line-height: 1.2;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="container-fluid py-4">
            <div class="row g-3 align-items-center mb-3">
                <div class="col">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="brand-mark">
                            <img src="{{ asset('favicon.ico') }}" alt="Site360Pro" style="width: 32px; height: 32px;">
                        </span>
                        <div>
                            <div class="fw-bold">Site360Pro</div>
                            <div class="text-secondary small">Daire raporu</div>
                        </div>
                    </div>
                    <div class="page-title">{{ $site->name }} - {{ $apartment->buildingBlock->name }}</div>
                    <h2 class="page-title mt-4">Daire No.: {{ $apartment->number }} - {{ $apartment->activeResident?->full_name ?? 'Sakin bilgisi yok' }} için {{ $year }} yılı aidat özeti.</h2>
                </div>
                <div class="col-auto d-flex align-items-center gap-2">
                    @if(isset($link->token))
                    <a href="{{ route('owner-reports.pdf', ['token' => $link->token, 'year' => $year]) }}" class="btn btn-outline-danger" download>
                        <x-icon name="file-type-pdf" class="me-1" /> PDF İndir
                    </a>
                    @else
                    <a href="{{ route('apartment.report.pdf', ['apartment' => $apartment->id, 'year' => $year]) }}" class="btn btn-outline-danger" download>
                        <x-icon name="file-type-pdf" class="me-1" /> PDF İndir
                    </a>
                    @endif
                    <form class="d-flex gap-2" method="get">
                        <div class="input-group">
                            <span class="input-group-text">Yıl Seçiniz</span>
                            <input class="form-control" type="number" name="year" value="{{ $year }}" min="2020" max="2100">
                        </div>
                        <button class="btn btn-primary" type="submit"><x-icon name="calendar-stats" class="me-1" /> Göster</button>
                    </form>
                    <button type="button" class="btn btn-icon btn-ghost-secondary theme-toggle-btn" data-theme-toggle aria-label="Karanlık Mod" title="Temayı Değiştir">
                        <span class="theme-icon-light"><x-icon name="moon" /></span>
                        <span class="theme-icon-dark"><x-icon name="sun" /></span>
                    </button>
                </div>
            </div>

            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-body border-bottom">
                        <div class="row row-cards mb-3">
                            <div class="col-4 col-md-4 col-lg-4">
                                <div class="card metric-card h-100">
                                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center py-2">
                                        <div class="fw-bold" style="color: var(--tblr-primary, deepskyblue);">Yıllık Tahakkuk</div>
                                        <div class="h1 mt-4 mb-1" style="color: deepskyblue !important; -webkit-text-fill-color: deepskyblue !important; background: none !important;">{{ $money($report['totals']['due']) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-md-4 col-lg-4">
                                <div class="card metric-card h-100">
                                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center py-2">
                                        <div class="fw-bold" style="color: var(--tblr-success, green);">Ödenen</div>
                                        <div class="h1 mt-4 mb-1" style="color: green !important; -webkit-text-fill-color: green !important; background: none !important;">{{ $money($report['totals']['paid']) }}</div>
                                        <div class="small mt-1" style="color: var(--tblr-success, green);">%{{ $report['collection_rate'] }} tahsilat</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 col-md-4 col-lg-4">
                                <div class="card metric-card h-100">
                                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center py-2">
                                        <div class="fw-bold" style="color: var(--tblr-danger, red);">Kalan Borç</div>
                                        <div class="h1 mt-4 mb-1" style="color: red !important; -webkit-text-fill-color: red !important; background: none !important;">{{ $money($report['totals']['pending']) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Aylık Aidat Durumu</h3>
                        <div class="card-actions text-secondary small">Link geçerliliği: {{ $link?->expires_at?->format('d.m.Y H:i') ?? 'Süresiz' }}</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Ay</th>
                                    <th style="width: 20%;">Tahakkuk</th>
                                    <th style="width: 20%;">Ödenen</th>
                                    <th style="width: 20%;">Kalan</th>
                                    <th style="width: 20%;">Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report['months'] as $month)
                                    <tr>
                                        <td class="fw-bold">{{ $month['name'] }}</td>
                                        <td>{{ $money($month['due']) }}</td>
                                        <td class="text-success">{{ $money($month['paid']) }}</td>
                                        <td class="text-danger">{{ $money($month['pending']) }}</td>
                                        <td>
                                            <span class="badge {{ $month['status'] === 'Ödendi' ? 'bg-green-lt' : ($month['status'] === 'Kayıt yok' ? 'bg-secondary-lt' : 'bg-red-lt') }}">
                                                {{ $month['status'] }}
                                            </span>
                                            @if ($month['source'])
                                                <span class="badge {{ $month['source_class'] }} ms-1">{{ $month['source'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="app-footer app-footer-fixed d-print-none">
        <div class="container-fluid px-2 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-danger">Site360Pro v1.0.8 (Lisanssızdır)</span>
            <span class="fw-bold text-danger">Geliştirici: Ömer Denizhan</span>
        </div>
    </footer>
</body>
</html>
