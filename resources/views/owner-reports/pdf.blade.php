@php
    $money = fn ($value) => number_format((float) $value, 2, ',', '.') . ' ₺';
    $apartment = $link?->apartment ?? $apartment;
    $site = $apartment->buildingBlock->site;
@endphp

<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>{{ $site->name }} - {{ $apartment->buildingBlock->name }} Aidat Raporu</title>
    <style>
        @page {
            size: A4 portrait;
            margin-top: 1.2cm;
            margin-bottom: 1.2cm;
            margin-left: 1.2cm;
            margin-right: 1.0cm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: "Times New Roman", Times, "DejaVu Serif", serif;
            font-size: 12pt;
            color: #000000;
            margin: 0;
            padding: 0;
            line-height: 1.25;
        }
        .header-site {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
        }
        .header-resident {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
        }
        .space-line {
            height: 14pt;
            line-height: 14pt;
        }
        .space-two-lines {
            height: 28pt;
            line-height: 28pt;
        }
        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
        }
        .metric-box {
            width: 33.333%;
            height: 2cm;
            max-height: 2cm;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
            font-size: 12pt;
            font-weight: bold;
            padding: 2px 4px;
        }
        .metric-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .metric-value {
            font-size: 12pt;
            font-weight: bold;
        }
        .metric-rate {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 2px;
        }
        .section-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
        }
        .report-table th,
        .report-table td {
            border: 1px solid #000000;
            height: 1.0cm;
            font-size: 12pt;
            vertical-align: middle;
            padding: 0 4px;
        }
        .report-table th {
            font-weight: bold;
            text-align: center;
        }
        .col-ay {
            width: 15%;
            text-align: center;
        }
        .col-tahakkuk {
            width: 15%;
            text-align: right;
        }
        .col-odenen {
            width: 15%;
            text-align: right;
        }
        .col-kalan {
            width: 15%;
            text-align: right;
        }
        .col-durum {
            width: 40%;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header-site">{{ $site->name }} - {{ $apartment->buildingBlock->name }}</div>
    <div class="space-line"></div>
    <div class="header-resident">Daire No: {{ $apartment->number }} - {{ $apartment->activeResident?->full_name ?? 'Sakin bilgisi yok' }} İçin {{ $year }} Yılı Aidat Özeti</div>
    <table class="metrics-table" style="margin-top: 15px;">
        <tr>
            <td class="metric-box">
                <div class="metric-title">Yıllık Tahakkuk</div>
                <div class="metric-value">{{ $money($report['totals']['due']) }}</div>
            </td>
            <td class="metric-box">
                <div class="metric-title">Ödenen</div>
                <div class="metric-value">{{ $money($report['totals']['paid']) }}</div>
                <div class="metric-rate">%{{ $report['collection_rate'] }} tahsilat</div>
            </td>
            <td class="metric-box">
                <div class="metric-title">Kalan Borç</div>
                <div class="metric-value">{{ $money($report['totals']['pending']) }}</div>
            </td>
        </tr>
    </table>
    
    <div class="space-two-lines"></div>
    <div class="section-title">Aylık Aidat Durumu</div>
    <div class="space-line"></div>
    <table class="report-table">
        <thead>
            <tr style="height: 1.0cm;">
                <th class="col-ay">AY</th>
                <th class="col-tahakkuk">TAHAKKUK</th>
                <th class="col-odenen">ÖDENEN</th>
                <th class="col-kalan">KALAN</th>
                <th class="col-durum">DURUM</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['months'] as $month)
                <tr style="height: 1.0cm;">
                    <td class="col-ay">{{ $month['name'] }}</td>
                    <td class="col-tahakkuk">{{ $money($month['due']) }}</td>
                    <td class="col-odenen">{{ $money($month['paid']) }}</td>
                    <td class="col-kalan">{{ $money($month['pending']) }}</td>
                    <td class="col-durum">
                        {{ $month['status'] }}
                        @if ($month['source'])
                            ({{ $month['source'] }})
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
