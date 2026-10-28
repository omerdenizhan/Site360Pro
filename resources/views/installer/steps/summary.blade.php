@extends('installer.layout')
@section('title', 'Özet ve Kurulum')
@section('heading', 'Özet ve Kurulumu Tamamla')

@php
    $app = $data['application'] ?? [];
    $admin = $data['admin'] ?? [];
    $site = $data['site'] ?? [];
    $comm = $data['communication'] ?? [];
    $visual = $data['visual'] ?? [];
    $yes = fn ($value) => $value ? 'Evet' : 'Hayır';
    $rows = [
        ['Uygulama', 'settings', [
            'Adres' => $app['app_url'] ?? '-',
            'Ortam' => ($app['app_env'] ?? '') === 'local' ? 'Geliştirme (local)' : 'Canlı (production)',
            'Saat dilimi' => $app['timezone'] ?? '-',
        ]],
        ['Veritabanı', 'database', [
            'Tür' => strtoupper($databaseDriver),
            'Sunucu' => isset($data['database']['host']) && $data['database']['host'] ? $data['database']['host'].':'.$data['database']['port'] : 'Yerel dosya (database/database.sqlite)',
            'Veritabanı adı' => $data['database']['database'] ?? '-',
            'Durum' => 'Tablolar oluşturuldu',
        ]],
        ['Yönetici', 'shield-lock', [
            'Ad soyad' => $admin['name'] ?? '-',
            'E-posta' => $admin['email'] ?? '-',
            'Şifre' => '••••••••',
            'Güvenlik sorusu' => $admin['security_question'] ?? '-',
        ]],
        ['Site ve Daireler', 'building-community', [
            'Site' => $site['name'] ?? '-',
            'Adres' => ($site['address'] ?? '') !== '' ? $site['address'] : '-',
            'Devir bakiyesi' => number_format((float) ($site['opening_balance'] ?? 0), 2, ',', '.').' ₺ ('.($site['opening_balance_date'] ?? '-').')',
            'Bloklar' => collect($site['blocks'] ?? [])->map(fn ($b) => $b['name'].' ('.$b['count'].')')->implode(', '),
            'Oluşturulacak daire' => $apartmentCount,
        ]],
        ['İletişim', 'mail', [
            'Genel adres' => ($comm['public_url'] ?? '') !== '' ? $comm['public_url'] : '-',
            'Destek e-postası' => ($comm['support_email'] ?? '') !== '' ? $comm['support_email'] : '-',
            'E-posta' => ($comm['mail']['mailer'] ?? 'log') === 'smtp' ? 'SMTP: '.($comm['mail']['host'] ?? '').':'.($comm['mail']['port'] ?? '') : 'Gönderim kapalı (günlüğe yazılır)',
            'WhatsApp API' => $yes($comm['whatsapp']['enabled'] ?? false),
        ]],
        ['Görsellik', 'palette', [
            'Vurgu rengi' => $visual['accent_color'] ?? '-',
            'Cam efekti' => $yes($visual['glass_effect'] ?? false),
            'Animasyonlar' => $yes($visual['animations_effect'] ?? false),
            'Kart hover' => $yes($visual['card_hover_effect'] ?? false),
            'Baloncuklar' => $yes($visual['enabled'] ?? false),
        ]],
    ];
@endphp

@section('content')
    @if ($ready)
        <div class="row g-3 mb-3">
            @foreach ($rows as [$title, $icon, $items])
                <div class="col-12 col-lg-6">
                    <div class="installer-group h-100">
                        <h3 class="installer-group-title"><x-icon :name="$icon" class="me-1" /> {{ $title }}</h3>
                        <dl class="installer-summary">
                            @foreach ($items as $label => $value)
                                <dt>{{ $label }}</dt>
                                <dd>{{ $value }}</dd>
                            @endforeach
                        </dl>
                    </div>
                </div>
            @endforeach
        </div>
        <form method="post" action="{{ route('install.finish') }}" data-installer-form data-busy-text="Kurulum tamamlanıyor, yönetici hesabı ve site kaydediliyor...">
            @csrf
            <label class="form-check">
                <input class="form-check-input" type="checkbox" name="confirm" value="1" required>
                <span class="form-check-label">Yukarıdaki bilgileri kontrol ettim ve kurulumu başlatmak istiyorum.</span>
            </label>
            @include('installer.partials.nav', ['previous' => 'visual', 'label' => 'Kurulumu Tamamla', 'busy' => 'finish'])
        </form>
    @else
        <div class="alert alert-warning"><x-icon name="alert-triangle" class="me-2" />Önceki adımlar tamamlanmadan kurulum bitirilemez.</div>
    @endif
@endsection
