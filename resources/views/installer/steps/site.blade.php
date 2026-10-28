@extends('installer.layout')
@section('title', 'Site ve Daireler')
@section('heading', 'Site, Bloklar ve Daireler')

@php
    $blocks = old('blocks', $values['blocks']);
    $blocks = array_values(is_array($blocks) ? $blocks : []);
    if ($blocks === []) {
        $blocks = [['name' => '', 'count' => 0]];
    }
@endphp

@section('content')
    <p class="text-secondary">Yöneteceğiniz siteyi tanımlayın. Daire sayısı girdiğiniz bloklar için 1'den başlayarak daireler otomatik oluşturulur; daha sonra Daireler ekranından düzenleyebilirsiniz.</p>
    <form method="post" action="{{ route('install.store', ['step' => 'site']) }}" data-installer-form data-busy-text="Site ve daireler kaydediliyor...">
        @csrf
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label required">Site / Apartman Adı</label>
                <input class="form-control" name="name" value="{{ old('name', $values['name']) }}" maxlength="160" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Adres</label>
                <input class="form-control" name="address" value="{{ old('address', $values['address']) }}" maxlength="255">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label required">Kasa Devir Bakiyesi (₺)</label>
                <input class="form-control" type="number" step="0.01" name="opening_balance" value="{{ old('opening_balance', $values['opening_balance']) }}" required>
                <div class="form-hint">Kasanın başlangıç tarihindeki tutarı. Bilmiyorsanız 0 bırakın; Site Yönetimi ekranından değiştirebilirsiniz.</div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label required">Devir Tarihi</label>
                <input class="form-control" type="date" name="opening_balance_date" value="{{ old('opening_balance_date', $values['opening_balance_date']) }}" required>
                <div class="form-hint">Kasa hesabı bu tarihin ayından başlar.</div>
            </div>
            <div class="col-12">
                <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-2">
                    <div>
                        <label class="form-label required mb-0">Bloklar ve Daire Sayıları</label>
                        <div class="form-hint">Blok yoksa tek bir blok (ör. "Ana Bina") tanımlayın.</div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-add-block><x-icon name="plus" class="me-1" /> Blok Ekle</button>
                </div>
                <div class="installer-blocks" id="blockRows" data-next-index="{{ count($blocks) }}">
                    @foreach ($blocks as $index => $block)
                        <div class="installer-block-row" data-block-row>
                            <input class="form-control" name="blocks[{{ $index }}][name]" value="{{ $block['name'] ?? '' }}" placeholder="Blok adı (ör. A Blok)" maxlength="80">
                            <input class="form-control" type="number" min="0" max="{{ config('installer.limits.apartments_per_block') }}" name="blocks[{{ $index }}][count]" value="{{ $block['count'] ?? 0 }}" placeholder="Daire sayısı" data-block-count>
                            <button type="button" class="btn btn-outline-danger btn-icon" data-remove-block title="Bloğu kaldır"><x-icon name="trash" /></button>
                        </div>
                    @endforeach
                </div>
                <template id="blockRowTemplate">
                    <div class="installer-block-row" data-block-row>
                        <input class="form-control" name="blocks[__INDEX__][name]" placeholder="Blok adı (ör. B Blok)" maxlength="80">
                        <input class="form-control" type="number" min="0" max="{{ config('installer.limits.apartments_per_block') }}" name="blocks[__INDEX__][count]" value="0" placeholder="Daire sayısı" data-block-count>
                        <button type="button" class="btn btn-outline-danger btn-icon" data-remove-block title="Bloğu kaldır"><x-icon name="trash" /></button>
                    </div>
                </template>
                <div class="form-hint mt-2">Toplam oluşturulacak daire: <strong data-apartment-total>0</strong></div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Kat Başına Daire Sayısı</label>
                <input class="form-control" type="number" min="0" max="50" name="per_floor" value="{{ old('per_floor', $values['per_floor']) }}">
                <div class="form-hint">Girerseniz daireler katlara göre dağıtılır (ör. 4 ise 1-4. daire 1. kat). 0 ise kat bilgisi boş bırakılır.</div>
            </div>
        </div>
        @include('installer.partials.nav')
    </form>
@endsection
