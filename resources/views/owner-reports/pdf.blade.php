@php
    $money = fn ($value) => number_format($value, 2, ',', '.') . ' ₺';
    $apartment = $link?->apartment ?? $apartment;
    $site = $apartment->buildingBlock->site;
@endphp
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Daire No: {{ $apartment->number }} - {{ $year }} Yılı Raporu</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .brand {
            font-size: 18px;
            font-weight: bold;
            color: #1b2a4e;
        }
        .sub-title {
            font-size: 11px;
            color: #666;
        }
        .page-title {
            font-size: 14px;
            margin-top: 10px;
            font-weight: bold;
        }
        .metrics-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .metric-box {
            border: 1px solid #e2e8f0;
            padding: 12px;
            text-align: center;
            background-color: #f8fafc;
        }
        .metric-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .metric-value {
            font-size: 16px;
            font-weight: bold;
        }
        .text-primary { color: #00bfff; }
        .text-success { color: #2fb344; }
        .text-danger { color: #d63939; }
        
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .report-table th, .report-table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #f1f5f9;
            font-size: 11px;
            text-transform: uppercase;
        }
        .badge {
            padding: 3px 6px;
            font-size: 10px;
            border-radius: 3px;
            display: inline-block;
        }
        .bg-success { background-color: #d2f4ea; color: #146c43; }
        .bg-danger { background-color: #f8d7da; color: #842029; }
        .bg-secondary { background-color: #e2e3e5; color: #41464b; }
        
        .footer {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 10px;
            font-size: 10px;
            color: #777;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <h1 class="page-title">Site360Pro</h1>
    <h1 class="page-title">{{ $site->name }} - {{ $apartment->buildingBlock->name }}</h1>
    <div>Daire No: {{ $apartment->number }} - {{ $apartment->activeResident?->full_name ?? 'Sakin bilgisi yok' }} için {{ $year }} yılı aidat özeti.</div>
    <br>
    <table class="metrics-table">
        <tr>
            <td class="metric-box" style="width: 33%;">
                <div class="metric-title text-primary">Yıllık Tahakkuk</div>
                <div class="metric-value text-primary">{{ $money($report['totals']['due']) }}</div>
            </td>
            <td class="metric-box" style="width: 33%;">
                <div class="metric-title text-success">Ödenen</div>
                <div class="metric-value text-success">{{ $money($report['totals']['paid']) }}</div>
                <div style="font-size: 9px;" class="text-success">%{{ $report['collection_rate'] }} tahsilat</div>
            </td>
            <td class="metric-box" style="width: 34%;">
                <div class="metric-title text-danger">Kalan Borç</div>
                <div class="metric-value text-danger">{{ $money($report['totals']['pending']) }}</div>
            </td>
        </tr>
    </table>

    <h3>Aylık Aidat Durumu</h3>
    <table class="report-table">
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
                    <td><strong>{{ $month['name'] }}</strong></td>
                    <td>{{ $money($month['due']) }}</td>
                    <td class="text-success">{{ $money($month['paid']) }}</td>
                    <td class="text-danger">{{ $money($month['pending']) }}</td>
                    <td>
                        <span class="badge {{ $month['status'] === 'Ödendi' ? 'bg-success' : ($month['status'] === 'Kayıt yok' ? 'bg-secondary' : 'bg-danger') }}">
                            {{ $month['status'] }}
                        </span>
                        @if ($month['source'])
                            <span class="badge bg-secondary">{{ $month['source'] }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
