@extends('installer.layout')
@section('title', 'Görsellik')
@section('heading', 'Görsellik')

@php
    $currentAccent = old('accent_color', $values['accent_color']);
    $currentAccentItem = collect($accentColors)->firstWhere('code', $currentAccent) ?? ['code' => $currentAccent, 'name' => 'Özel Renk'];
    $flag = fn (string $key) => (bool) old($key, $values[$key]);
@endphp

@section('content')
    <p class="text-secondary">Arayüzün görünümünü seçin. Tüm seçenekler kurulumdan sonra Ayarlar &rarr; Görsellik ekranından değiştirilebilir.</p>
    <form method="post" action="{{ route('install.store', ['step' => 'visual']) }}" data-installer-form>
        @csrf
        <h3 class="installer-section-title"><x-icon name="palette" class="me-1" /> Arayüz Efektleri</h3>
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label fw-bold">Vurgu Rengi</label>
                <input type="hidden" name="accent_color" id="accentColorInput" value="{{ $currentAccentItem['code'] }}">
                <div class="accent-picker" id="accentPicker">
                    <button type="button" class="form-select accent-picker-btn" id="accentPickerBtn" aria-haspopup="listbox" aria-expanded="false">
                        <span class="accent-swatch" id="accentPickerSwatch" style="background-color: {{ $currentAccentItem['code'] }};"></span>
                        <span class="accent-picker-text" id="accentPickerText">{{ $currentAccentItem['name'] }} <span class="text-secondary">({{ $currentAccentItem['code'] }})</span></span>
                    </button>
                    <ul class="accent-picker-list" id="accentPickerList" role="listbox" hidden>
                        @foreach ($accentColors as $color)
                            <li role="option" tabindex="0" class="accent-picker-item {{ $currentAccentItem['code'] === $color['code'] ? 'is-selected' : '' }}" data-code="{{ $color['code'] }}" data-name="{{ $color['name'] }}" aria-selected="{{ $currentAccentItem['code'] === $color['code'] ? 'true' : 'false' }}">
                                <span class="accent-swatch" style="background-color: {{ $color['code'] }};"></span>
                                <span class="accent-picker-text">{{ $color['name'] }} <span class="text-secondary">({{ $color['code'] }})</span></span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="form-hint mt-2">Butonların, sekmelerin ve vurgulu alanların rengi.</div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" name="glass_effect" value="1" @checked($flag('glass_effect'))>
                    <span class="form-check-label fw-bold">Cam (Bulanıklık Efekti)</span>
                </label>
                <div class="form-hint">Panellerde buzlu cam görünümü.</div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" name="animations_effect" value="1" @checked($flag('animations_effect'))>
                    <span class="form-check-label fw-bold">Geçiş ve Çubuk Animasyonları</span>
                </label>
                <div class="form-hint">Akıcı geçişler ve çubuk animasyonları.</div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" name="card_hover_effect" value="1" @checked($flag('card_hover_effect'))>
                    <span class="form-check-label fw-bold">Kart Hover Animasyonları</span>
                </label>
                <div class="form-hint">Kartların üzerine gelindiğinde renkli vurgu.</div>
            </div>
        </div>
        <h3 class="installer-section-title"><x-icon name="sparkles" class="me-1" /> Baloncuk Efektleri</h3>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="enabled" value="1" @checked($flag('enabled'))>
                    <span class="form-check-label fw-bold">Baloncuk efektini etkinleştir</span>
                </label>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label fw-bold">Baloncuk Sayısı</label>
                <select class="form-select" name="count">
                    @foreach ([10 => '10 Adet (Hafif)', 20 => '20 Adet (Önerilen)', 35 => '35 Adet (Yoğun)', 50 => '50 Adet (Maksimum)'] as $val => $text)
                        <option value="{{ $val }}" @selected((int) old('count', $values['count']) === $val)>{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label fw-bold">Hız</label>
                <select class="form-select" name="speed">
                    @foreach (['slow' => 'Yavaş / Dingin', 'normal' => 'Normal Hız', 'fast' => 'Hızlı / Dinamik'] as $val => $text)
                        <option value="{{ $val }}" @selected(old('speed', $values['speed']) === $val)>{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label fw-bold">Şeffaflık / Opaklık</label>
                <select class="form-select" name="opacity">
                    @foreach ([15 => '%15 (Çok Hafif)', 30 => '%30 (Dengeli)', 50 => '%50 (Belirgin)'] as $val => $text)
                        <option value="{{ $val }}" @selected((int) old('opacity', $values['opacity']) === $val)>{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label fw-bold">Renk Modu</label>
                <select class="form-select" name="color_mode">
                    @foreach (['accent' => 'Seçili Vurgu Rengine Göre', 'blue' => 'Gökyüzü Mavi Tonları', 'pastel' => 'Pastel Renkler', 'white' => 'Buzlu Beyaz / Pastel'] as $val => $text)
                        <option value="{{ $val }}" @selected(old('color_mode', $values['color_mode']) === $val)>{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label fw-bold">Boyut</label>
                <select class="form-select" name="size">
                    @foreach (['small' => 'Küçük', 'medium' => 'Orta', 'large' => 'Büyük', 'mixed' => 'Karışık'] as $val => $text)
                        <option value="{{ $val }}" @selected(old('size', $values['size']) === $val)>{{ $text }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @include('installer.partials.nav')
    </form>
@endsection
