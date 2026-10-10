@php
    $money = fn ($value) => number_format((float) $value, 2, ',', '.') . ' ₺';
    $headerTitle = $month
        ? ($site->name . ' - ' . $blockName . ' - ' . ($months[$month] ?? '') . ' ' . $year . ' Finansal Raporu')
        : ($site->name . ' - ' . $blockName . ' - ' . $year . ' Yılı Finansal Raporu');
@endphp
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>{{ $headerTitle }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin-top: 1.2cm;
            margin-bottom: 1.2cm;
            margin-left: 1.0cm;
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
        .header-title {
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
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
        }
        .summary-box {
            width: 16.66%;
            height: 2cm;
            max-height: 2.5cm;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
            font-size: 12pt;
            font-weight: bold;
            padding: 2px 4px;
        }
        .box-sub {
            font-size: 8pt;
            font-weight: normal;
            margin-top: 3px;
        }
        .box-title {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .box-value {
            font-size: 11pt;
            font-weight: bold;
        }
        table.detail {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        table.detail th,
        table.detail td {
            border: 1px solid #000000;
            height: 1.0cm;
            padding: 2px 5px;
            font-size: 11pt;
            vertical-align: middle;
        }
        table.detail th {
            font-weight: bold;
            text-align: center;
        }
        .right {
            text-align: right;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header-title">{{ $headerTitle }}</div>
    <div class="space-line"></div>
    <table class="summary-table">
        <tr>
            <td class="summary-box">
                <div class="box-title">Kasa Devri</div>
                <div class="box-value">{{ $money($report['totals']['carryover']) }}</div>
                <div class="box-sub">{{ $month ? 'Önceki aydan devreden kasa' : 'Yıl başı kasa devri' }}</div>
            </td>
            <td class="summary-box">
                <div class="box-title">{{ $month ? 'Dönem Tahakkuku' : 'Yıllık Tahakkuk' }}</div>
                <div class="box-value">{{ $money($report['totals']['due']) }}</div>
                <div class="box-sub">Toplam borçlandırma</div>
            </td>
            <td class="summary-box">
                <div class="box-title">{{ $month ? 'Dönem Tahsilatı' : 'Yıllık Tahsilat' }}</div>
                <div class="box-value">{{ $money($report['totals']['paid']) }}</div>
                <div class="box-sub">%{{ $report['collection_rate'] }} tahsilat</div>
            </td>
            <td class="summary-box">
                <div class="box-title">{{ $month ? 'Dönem Gelirler Toplamı' : 'Yıllık Gelirler' }}</div>
                <div class="box-value">{{ $money($report['totals']['income_total']) }}</div>
                <div class="box-sub">Kayıtlı gelirler toplamı</div>
            </td>
            <td class="summary-box">
                <div class="box-title">{{ $month ? 'Dönem Giderler Toplamı' : 'Yıllık Giderler' }}</div>
                <div class="box-value">{{ $money($report['totals']['expense']) }}</div>
                <div class="box-sub">Kayıtlı giderler toplamı</div>
            </td>
            <td class="summary-box">
                <div class="box-title">{{ $month ? 'Dönem Sonu Kasa' : 'Yıl Sonu Kasa' }}</div>
                <div class="box-value">{{ $money($report['totals']['balance']) }}</div>
                <div class="box-sub">Gelirler - Giderler</div>
            </td>
        </tr>
    </table>
    <div class="space-line"></div>
    <table class="detail">
        <thead>
            <tr style="height: 1.0cm;">
                <th>Ay</th>
                <th class="right">Devir</th>
                <th class="right">Tahakkuk</th>
                <th class="right">Tahsilat</th>
                <th class="right">Gelir</th>
                <th class="right">Gelir Toplamı</th>
                <th class="right">Gider</th>
                <th class="right">Kalan Borç</th>
                <th class="right">Kasa</th>
                <th class="center">Oran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['months'] as $m)
                <tr style="height: 1.0cm;">
                    <td><strong>{{ $m['name'] }}</strong></td>
                    <td class="right">{{ $money($m['carryover']) }}</td>
                    <td class="right">{{ $money($m['due']) }}</td>
                    <td class="right">{{ $money($m['paid']) }}</td>
                    <td class="right">{{ $money($m['income']) }}</td>
                    <td class="right">{{ $money($m['carryover'] + $m['paid'] + $m['income']) }}</td>
                    <td class="right">{{ $money($m['expense']) }}</td>
                    <td class="right">{{ $money($m['pending']) }}</td>
                    <td class="right">{{ $money($m['balance']) }}</td>
                    <td class="center">%{{ $m['rate'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
