@php
    $money = fn ($value) => '₺' . number_format((float) $value, 2, ',', '.');
    $apartment = $link->apartment;
    $site = $apartment->buildingBlock->site;
@endphp

<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daire Raporu - {{ $site->name }}</title>
    <script>
        (function() {
            var theme = localStorage.getItem('SiteAptFinansYonetimi_theme') || '{{ session('theme', 'light') }}';
            document.documentElement.setAttribute('data-bs-theme', theme);
            document.documentElement.classList.add(theme === 'dark' ? 'theme-dark' : 'theme-light');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="page page-center">
        <div class="container-xl py-4">
            <div class="row g-3 align-items-center mb-3">
                <div class="col">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="brand-mark"><x-icon name="building-skyscraper" /></span>
                        <div>
                            <div class="fw-bold">Site360Pro</div>
                            <div class="text-secondary small">Daire raporu</div>
                        </div>
                    </div>
                    <div class="page-pretitle">{{ $site->name }}</div>
                    <h2 class="page-title">{{ $apartment->buildingBlock->name }} / {{ $apartment->number }}</h2>
                    <div class="text-secondary mt-1">
                        {{ $apartment->activeResident?->full_name ?? 'Sakin bilgisi yok' }} için {{ $year }} yılı aidat özeti.
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center gap-2">
                    <form class="d-flex gap-2" method="get">
                        <input class="form-control" type="number" name="year" value="{{ $year }}" min="2020" max="2100">
                        <button class="btn" type="submit"><x-icon name="calendar-stats" class="me-1" /> Göster</button>
                    </form>
                    <button type="button" class="btn btn-icon btn-ghost-secondary theme-toggle-btn" data-theme-toggle aria-label="Karanlık Mod" title="Temayı Değiştir">
                        <span class="theme-icon-light"><x-icon name="moon" /></span>
                        <span class="theme-icon-dark"><x-icon name="sun" /></span>
                    </button>
                </div>
            </div>

            <div class="row row-cards mb-3">
                <div class="col-4 col-md-4 col-lg-4">
                    <div class="card metric-card"><div class="card-body"><div class="text-secondary">Yıllık Tahakkuk</div><div class="h1">{{ $money($report['totals']['due']) }}</div></div></div>
                </div>
                <div class="col-4 col-md-4 col-lg-4">
                    <div class="card metric-card"><div class="card-body"><div class="text-secondary">Ödenen</div><div class="h1 text-success">{{ $money($report['totals']['paid']) }}</div><div class="text-secondary">%{{ $report['collection_rate'] }} tahsilat</div></div></div>
                </div>
                <div class="col-4 col-md-4 col-lg-4">
                    <div class="card metric-card"><div class="card-body"><div class="text-secondary">Kalan Borç</div><div class="h1 text-danger">{{ $money($report['totals']['pending']) }}</div></div></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aylık Aidat Durumu</h3>
                    <div class="card-actions text-secondary small">
                        Link geçerliliği: {{ $link->expires_at?->format('d.m.Y H:i') }}
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Ay</th>
                                <th>Tahakkuk</th>
                                <th>Ödenen</th>
                                <th>Kalan</th>
                                <th>Durum</th>
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
</body>
</html>
