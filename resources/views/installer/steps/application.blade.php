@extends('installer.layout')
@section('title', 'Uygulama')
@section('heading', 'Uygulama Ayarları')

@php
    $zones = array_merge(['Europe/Istanbul', 'UTC'], array_values(array_diff(
        array_merge(\DateTimeZone::listIdentifiers(\DateTimeZone::EUROPE), \DateTimeZone::listIdentifiers(\DateTimeZone::ASIA)),
        ['Europe/Istanbul'],
    )));
    $env = old('app_env', $values['app_env']);
@endphp

@section('content')
    <form method="post" action="{{ route('install.store', ['step' => 'application']) }}" data-installer-form>
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label required">Uygulama Adresi (APP_URL)</label>
                <input class="form-control" type="url" name="app_url" value="{{ old('app_url', $values['app_url']) }}" placeholder="https://site.example.com" required>
                <div class="form-hint">Panelin tarayıcıda açıldığı adres. Bağlantılar, PDF ve e-postalar bu adresi kullanır. Sonunda eğik çizgi olmamalıdır.</div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label required">Çalışma Ortamı</label>
                <select class="form-select" name="app_env" data-env-select>
                    <option value="production" @selected($env === 'production')>Canlı (production)</option>
                    <option value="local" @selected($env === 'local')>Geliştirme (local)</option>
                </select>
                <div class="form-hint">Gerçek kullanımda "Canlı" seçin.</div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label required">Saat Dilimi</label>
                <select class="form-select" name="timezone">
                    @foreach ($zones as $zone)
                        <option value="{{ $zone }}" @selected(old('timezone', $values['timezone']) === $zone)>{{ $zone }}</option>
                    @endforeach
                </select>
                <div class="form-hint">Tarih ve saatlerin hesaplanacağı saat dilimi.</div>
            </div>
            <div class="col-12" data-debug-panel @if ($env !== 'local') hidden @endif>
                <label class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="app_debug" value="1" @checked(old('app_debug', $values['app_debug']))>
                    <span class="form-check-label fw-bold">Hata ayıklama modu (APP_DEBUG)</span>
                </label>
                <div class="form-hint">Yalnızca geliştirme içindir. Canlı ortamda hata ayrıntıları ziyaretçilere gösterilmez.</div>
            </div>
        </div>
        @include('installer.partials.nav')
    </form>
@endsection
