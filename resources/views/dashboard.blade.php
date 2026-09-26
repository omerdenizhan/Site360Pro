@extends('layouts.app', ['title' => 'Ana Panel  - Site360Pro'])

@php
    $money = fn ($value) => number_format($value, 2, ',', '.') . ' ₺';
@endphp

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-3 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Bugünkü işlerin özeti</div>
                    <h2 class="page-title">{{ $site->name }} {{ $blocks->firstWhere('id', $blockId)?->name }}</h2>
                    <div class="text-secondary mt-1">{{ $periodName }} dönemi için tahakkuk, tahsilat, kasa ve takip durumu.</div>
                </div>
                <div class="col-auto">
                    <div class="btn-list">
                        <a class="btn" href="{{ route('payments.create') }}"><x-icon name="wallet" class=" me-1" /> Ödeme Al</a>
                        <a class="btn btn-primary" href="{{ route('dues.create') }}"><x-icon name="plus" class=" me-1" /> Tahakkuk Oluştur</a>
                    </div>
                </div>
            </div>

            <div class="row mt-6">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body border-bottom">
                            <form class="d-flex justify-content-between align-items-end w-100" method="get" action="{{ route('dashboard') }}">
                                <!-- (Mobil | Tablet | Masaüstü) -->
                                <div class="col-3 col-md-3 col-lg-3">
                                    <label class="form-label">Site</label>
                                    <select class="form-select" name="site_id" aria-label="Site seç">
                                        @foreach ($sites as $filterSite)
                                            <option value="{{ $filterSite->id }}" @selected($site->id === $filterSite->id)>{{ $filterSite->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-5 col-md-5 col-lg-5">
                                    <label class="form-label">Blok Adı</label>
                                    <select class="form-select" name="block_id" aria-label="Blok seç">
                                        <option value="">Tüm bloklar</option>
                                        @foreach ($blocks as $block)
                                            <option value="{{ $block->id }}" @selected($blockId === $block->id)>{{ $block->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-1 col-md-1 col-lg-1">
                                    <label class="form-label">Yıl</label>
                                    <input class="form-control text-center" type="number" name="year" value="{{ $periodYear }}" min="2020" max="2100">
                                </div>
                                <div class="col-2 col-md-2 col-lg-2">
                                    <label class="form-label">Ay</label>
                                    <select class="form-select" name="month">
                                        @foreach ($months as $number => $name)
                                            <option value="{{ $number }}" @selected($periodMonth === $number)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-auto col-md-auto col-lg-auto">
                                    <button class="btn btn-primary" type="submit"><x-icon name="filter"/></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-body">
            <div class="container-xl">
                <div class="row row-cards mb-3">
                    <div class="col-sm-6 col-xl-3">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary fw-semibold" style="color: green !important;">Kasa Bakiyesi</span>
                                    <span class="avatar bg-green-lt"><x-icon name="building-bank" class="" /></span>
                                </div>
                                <div class="h1 mt-4 mb-1" style="color: green !important; -webkit-text-fill-color: green !important; background: none !important;">{{ $money($cashBalance) }}</div>
                                <div class="text-secondary small" style="color: green !important;">Toplam gelir - gider</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary fw-semibold" style="color: deepskyblue !important;">Dönem Tahakkuku</span>
                                    <span class="avatar bg-deepskyblue-lt"><x-icon name="receipt" class="" /></span>
                                </div>
                                <div class="h1 mt-4 mb-1" style="color: deepskyblue !important; -webkit-text-fill-color: deepskyblue !important; background: none !important;">{{ $money($summary->total_due) }}</div>
                                <div class="text-secondary small" style="color: deepskyblue !important;">{{ (int) $summary->due_count }} daire borçlandırıldı</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary fw-semibold" style="color: orange !important;">Tahsil Edilen</span>
                                    <span class="avatar bg-orange-lt"><x-icon name="circle-check" class="" /></span>
                                </div>
                                <div class="h1 mt-4 mb-1" style="color: orange !important; -webkit-text-fill-color: orange !important; background: none !important;">{{ $money($summary->total_paid) }}</div>
                                <div class="text-secondary small" style="color: orange !important;">%{{ $collectionRate }} tahsilat oranı</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary fw-semibold" style="color: red !important;">Kalan Borç</span>
                                    <span class="avatar bg-red-lt"><x-icon name="alert-triangle" class="" /></span>
                                </div>
                                <div class="h1 mt-4 mb-1" style="color: red !important; -webkit-text-fill-color: red !important; background: none !important;">{{ $money($summary->total_pending) }}</div>
                                <div class="text-secondary small"style="color: red !important;">{{ (int) $summary->unpaid_count + (int) $summary->partial_count }} daire takipte</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-cards">
                    <div class="col-xl-9">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Daire Bazlı Aidat Durumu</h3>
                                <div class="card-actions"><a class="btn btn-sm" href="{{ route('dues.index', ['site_id' => $site->id, 'block_id' => $blockId, 'year' => $periodYear, 'month' => $periodMonth]) }}">Tümünü Gör</a></div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">Daire</th>
                                            <th style="width: 55%;">Sakin</th>
                                            <th style="width: 10%;">Tahakkuk</th>
                                            <th style="width: 10%;">Ödenen</th>
                                            <th style="width: 10%;">Kalan</th>
                                            <th style="width: 10%;">Durum</th>
                                            <th class="w-1"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($dues->take(7) as $due)
                                        @php
                                            $paid = (float) $due->payments->sum('amount');
                                            $amount = (float) $due->amount;
                                            $pending = max(0, $amount - $paid);
                                            $isLate = $pending > 0 && $due->due_date->isPast();
                                            $statusClass = $pending <= 0 ? 'bg-green-lt' : ($paid > 0 ? 'bg-yellow-lt' : 'bg-red-lt');
                                            $statusLabel = $pending <= 0 ? 'Ödendi' : ($paid > 0 ? 'Kısmi' : ($isLate ? 'Gecikti' : 'Bekliyor'));
                                            $sourceLabel = $paid > 0 ? $due->paymentSourceLabel() : null;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $due->apartment->number }}</div>
                                            </td>
                                            <td>{{ $due->apartment->activeResident?->full_name ?? 'Sakin yok' }}
                                                <div class="text-secondary small">{{ $due->apartment->buildingBlock->name }}</div>
                                            </td>
                                            <td>{{ $money($amount) }}</td>
                                            <td>{{ $money($paid) }}</td>
                                            <td class="{{ $pending > 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">{{ $money($pending) }}</td>
                                            <td>
                                                <div class="d-flex flex-column align-items-start gap-1">
                                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                                    @if ($sourceLabel)
                                                        <span class="badge {{ $due->paymentSourceBadgeClass() }}">{{ $sourceLabel }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if ($pending > 0)
                                                    <a class="btn btn-sm" href="{{ route('payments.create', ['due_id' => $due->id]) }}">Öde</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-secondary py-5">Bu dönem için aidat kaydı bulunmamaktadır.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="row row-cards">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header"><h3 class="card-title">Hızlı İşlemler</h3></div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a class="btn justify-content-start op-command" href="{{ route('payments.create') }}"><x-icon name="wallet" class=" me-2" />Ödeme Al</a>
                                            <a class="btn justify-content-start op-command" href="{{ route('apartments.create') }}"><x-icon name="building-plus" class=" me-2" />Yeni Daire Ekle</a>
                                            <a class="btn justify-content-start op-command" href="{{ route('dues.create') }}"><x-icon name="receipt" class=" me-2" />Toplu Tahakkuk Oluştur</a>
                                            <a class="btn justify-content-start op-command" href="{{ route('incomes.create') }}"><x-icon name="cash" class=" me-2" />Gelir Ekle</a>
                                            <a class="btn justify-content-start op-command" href="{{ route('expenses.create') }}"><x-icon name="report-money" class=" me-2" />Gider Ekle</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="collection-ring dashboard-ring" style="--rate: {{ $collectionRate }}%;">
                                                    <div class="collection-ring-inner">
                                                        <div><strong>%{{ $collectionRate }}</strong><span class="text-secondary fw-semibold">Tahsilat</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3 class="card-title mb-2">Dönem Kontrolü</h3>
                                                <div class="text-secondary">{{ (int) $apartmentStats->occupied }} dolu, {{ (int) $apartmentStats->empty_count }} boş daire var.</div>
                                                <div class="mt-3">
                                                    <a class="btn btn-sm" href="{{ route('apartments.index') }}">Daireleri Kontrol Et</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Son Tahsilatlar</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">DAİRE</th>
                                            <th style="width: 50%;">SAKİN</th>
                                            <th style="width: 20%;">MAKBUZ NO.</th>
                                            <th style="width: 15%;">DURUM</th>
                                            <th style="width: 10%;">ÖDEME</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($recentPayments->take(5) as $payment)
                                            <tr>
                                                <td>{{ $payment->due->apartment->number }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $payment->due->apartment->activeResident?->full_name ?? 'Sakin yok' }}</div>
                                                    <div class="text-secondary small">{{ $payment->due->apartment->buildingBlock?->name ?? 'Apartman bilgisi yok' }}</div>
                                                </td>
                                                <td class="ps-8">{{ $payment->receipt_no }}</td>
                                                <td><span class="badge {{ $payment->source_badge_class }}">{{ $payment->source_label }}</span></td>
                                                <td><span class="fw-bold text-success">{{ $money($payment->amount) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-secondary text-center py-3">Bu dönem için tahsilat kaydı bulunmamaktadır.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Öncelikli Takip</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">DAİRE</th>
                                            <th style="width: 70%;">SAKİN</th>
                                            <th style="width: 15%;">DURUM</th>
                                            <th style="width: 10%;">KALAN BORÇ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($followItems->take(5) as $item)
                                            <tr>
                                                <td>{{ $item['due']->apartment->number }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $item['due']->apartment->activeResident?->full_name ?? 'Sakin yok' }}</div>
                                                    <div class="text-secondary small">{{ $item['due']->apartment->buildingBlock?->name ?? 'Apartman bilgisi yok' }}</div>
                                                </td>
                                                <td>
                                                    @if ($item['paid'] > 0)
                                                        <span class="badge bg-yellow-lt text-yellow">Kısmi</span>
                                                    @else
                                                        <span class="badge bg-red-lt text-red">Ödenmedi</span>
                                                    @endif
                                                </td>
                                                <td><span class="fw-bold text-danger">{{ $money($item['pending']) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-secondary text-center py-3">Bu dönem için takip edilecek borç kaydı bulunmamaktadır.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Son Gelirler</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 80%;">GELİR AÇIKLAMASI</th>
                                            <th style="width: 20%;">GELİR MİKTARI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($incomes->take(5) as $income)
                                            <tr>
                                                <td><div class="fw-semibold">{{ $income->category }}</div>
                                                    @if ($income->description)
                                                        <div class="text-secondary small">{{ $income->description }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td><span class="fw-bold text-success">{{ $money($income->amount) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-secondary text-center py-3">Bu dönem için gelir kaydı bulunmamaktadır.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Son Giderler</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 80%;">GİDER AÇIKLAMASI</th>
                                            <th style="width: 20%;">GİDER MİKTARI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($expenses->take(5) as $expense)
                                            <tr>
                                                <td><div class="fw-semibold">{{ $expense->category }}</div>
                                                    @if ($expense->description)
                                                        <div class="text-secondary small">{{ $expense->description }}</div>
                                                    @endif
                                                </td>
                                                <td><span class="fw-bold text-danger">{{ $money($expense->amount) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-secondary text-center py-3">Bu dönem için gider kaydı bulunmamaktadır.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>
@endsection
