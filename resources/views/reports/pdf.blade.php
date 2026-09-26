@php
    $money = fn ($value) => number_format($value, 2, ',', '.') . ' ₺';
@endphp

<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>{{ $site->name }} - {{ $periodLabel }} Finansal Raporu</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px; 
            color: #1f2937; 
            padding: 10px;
        }
        
        .brand {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }

        h1.title { 
            margin: 0 0 15px 0; 
            font-size: 18px; 
            color: #0f172a;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
        }

        /* Renk Tanımlamaları */
        .text-primary { color: #00bfff !important; }
        .text-warning { color: #f59e0b !important; }
        .text-danger { color: #ef4444 !important; }
        .text-success { color: #10b981 !important; }

        /* Özet Kartlar Tablosu */
        .summary { 
            width: 100%; 
            margin-bottom: 20px; 
            border-collapse: separate; 
            border-spacing: 6px; 
        }

        .summary td { 
            width: 25%; 
            padding: 10px; 
            border: 1px solid #e2e8f0; 
            border-radius: 4px;
            background-color: #f8fafc;
            text-align: center;
        }

        .summary td.card-due { border-color: #38bdf8; background-color: #f0f9ff; }
        .summary td.card-paid { border-color: #fbbf24; background-color: #fffbeb; }
        .summary td.card-expense { border-color: #fca5a5; background-color: #fef2f2; }
        .summary td.card-balance { border-color: #6ee7b7; background-color: #ecfdf5; }

        .label { 
            font-size: 10px; 
            font-weight: bold; 
            display: block;
        }

        .value { 
            display: block; 
            margin-top: 6px; 
            font-size: 15px; 
            font-weight: bold; 
        }

        /* Detay Tablosu */
        table.detail { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
        }

        table.detail th, table.detail td { 
            padding: 7px 8px; 
            border: 1px solid #cbd5e1; 
            font-size: 10px;
        }

        table.detail th { 
            background: #1e293b; 
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
        }

        table.detail tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .right { text-align: right; }
        .center { text-align: center; }
    </style>
</head>

<body>

    <h1 class="page-title">Site360Pro</h1>
    <h1 class="page-title">{{ $site->name }} - {{ $periodLabel }} Finansal Raporu</h1>

    {{-- Metrik Kartlar --}}
    <table class="summary">
        <tr>
            <td class="card-due">
                <span class="label text-primary">{{ $month ? 'Dönem Tahakkuku' : 'Yıllık Tahakkuk' }}</span>
                <span class="value text-primary">{{ $money($report['totals']['due']) }}</span>
            </td>
            <td class="card-paid">
                <span class="label text-warning">{{ $month ? 'Dönem Tahsilatı' : 'Yıllık Tahsilat' }}</span>
                <span class="value text-warning">{{ $money($report['totals']['paid']) }}</span>
            </td>
            <td class="card-expense">
                <span class="label text-danger">{{ $month ? 'Dönem Gideri' : 'Yıllık Gider' }}</span>
                <span class="value text-danger">{{ $money($report['totals']['expense']) }}</span>
            </td>
            <td class="card-balance">
                <span class="label text-success">Net Kasa Etkisi</span>
                <span class="value text-success">{{ $money($report['totals']['balance']) }}</span>
            </td>
        </tr>
    </table>

    {{-- Aylık Detay Tablosu --}}
    <table class="detail">
        <thead>
            <tr>
                <th>Ay</th>
                <th class="right">Tahakkuk</th>
                <th class="right">Tahsilat</th>
                <th class="right">Gider</th>
                <th class="right">Kalan Borç</th>
                <th class="right">Net</th>
                <th class="center">Tahsilat Oranı</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['months'] as $m)
                <tr>
                    <td><strong>{{ $m['name'] }}</strong></td>
                    <td class="right text-primary"><strong>{{ $money($m['due']) }}</strong></td>
                    <td class="right text-warning"><strong>{{ $money($m['paid']) }}</strong></td>
                    <td class="right text-danger">{{ $money($m['expense']) }}</td>
                    <td class="right text-danger">{{ $money($m['pending']) }}</td>
                    <td class="right {{ $m['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                        <strong>{{ $money($m['balance']) }}</strong>
                    </td>
                    <td class="center">%{{ $m['rate'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
