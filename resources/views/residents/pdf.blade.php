<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>{{ $site->name }} - {{ $blockLabel }} Sakin Bilgileri</title>
    <style>
        @page {
            margin: 1cm 0.6cm 0.8cm 0.6cm;
        }
        body {
            font-family: "Times New Roman", Times, "DejaVu Serif", serif;
            font-size: 9.75px;
            color: #1f2937;
            margin: 0;
        }
        h1.title {
            margin: 0 0 0 0;
            font-size: 11pt;
            color: #0f172a;
            padding-bottom: 1px;
            text-align: center;
        }
        table.detail {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.detail th, table.detail td {
            padding: 0.3px 0.3px;
            border: 1px solid #cbd5e1;
            font-size: 9.75px;
            word-wrap: break-word;
        }
        table.detail th {
            background: #1e293b;
            color: #ffffff;
            font-weight: bold;
        }
        table.detail tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        thead {
            display: table-header-group;
        }
        tr {
            page-break-inside: avoid;
        }
        .center {
            text-align: center;
        }
        .left {
            text-align: left;
        }
    </style>
</head>

<body>
    <h1 class="title">{{ $site->name }} - {{ $blockLabel }} Sakin Bilgileri</h1>
    <table class="detail">
        <thead>
            <tr>
                <th class="center" style="width: 2%;">#</th>
                <th class="center" style="width: 3%;">Kat</th>
                <th class="center" style="width: 3%;">Daire</th>
                <th class="left" style="width: 27%;">Adı Soyadı</th>
                <th class="left" style="width: auto;">Görev Yeri</th>
                <th class="left" style="width: 11%;">Araç Marka / Modeli 1</th>
                <th class="center" style="width: 6%;">Araç Plakası 1</th>
                <th class="left" style="width: 11%;">Araç Marka / Modeli 2</th>
                <th class="center" style="width: 6%;">Araç Plakası 2</th>
            </tr>
        </thead>
        <tbody>
            @php
                $dash = fn ($value) => filled($value) ? $value : '-';
                $rowNo = 0;
            @endphp
            @forelse ($apartments as $apartment)
                @php

                    $occupants = $apartment->residents->isNotEmpty() ? $apartment->residents : collect([null]);
                @endphp
                @foreach ($occupants as $resident)
                    @php $rowNo++; @endphp
                    <tr>
                        <td class="center">{{ $rowNo }}</td>
                        <td class="center">{{ $dash($apartment->floor_no) }}</td>
                        <td class="center">{{ $apartment->number }}</td>
                        <td class="left">{{ $dash($resident?->full_name) }}</td>
                        <td class="left">{{ $dash($resident?->workplace) }}</td>
                        <td class="left">{{ $dash($resident?->vehicle_model_1) }}</td>
                        <td class="center">{{ $dash($resident?->vehicle_plate_1) }}</td>
                        <td class="left">{{ $dash($resident?->vehicle_model_2) }}</td>
                        <td class="center">{{ $dash($resident?->vehicle_plate_2) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="9" class="center">Kayıt bulunamadı.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
