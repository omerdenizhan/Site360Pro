@extends('installer.layout')
@section('title', 'İletişim')
@section('heading', 'İletişim ve Entegrasyonlar')

@php
    $mailer = old('mail_mailer', $values['mail_mailer']);
    $whatsapp = (bool) old('whatsapp_enabled', $values['whatsapp_enabled']);
@endphp

@section('content')
    <p class="text-secondary">Bu adımdaki her şey isteğe bağlıdır ve kurulumdan sonra Ayarlar ekranından değiştirilebilir.</p>
    <form method="post" action="{{ route('install.store', ['step' => 'communication']) }}" data-installer-form data-mail-test-url="{{ route('install.mail.test') }}">
        @csrf
        <h3 class="installer-section-title"><x-icon name="settings-cog" class="me-1" /> Genel</h3>
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <label class="form-label">Genel Erişim Adresi</label>
                <input class="form-control" type="url" name="public_url" value="{{ old('public_url', $values['public_url']) }}" placeholder="https://site.example.com">
                <div class="form-hint">Sakinlerle paylaşılan rapor bağlantılarında kullanılır.</div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Destek E-postası</label>
                <input class="form-control" type="email" name="support_email" value="{{ old('support_email', $values['support_email']) }}">
            </div>
        </div>
        <h3 class="installer-section-title"><x-icon name="mail" class="me-1" /> E-posta (Duyuru Gönderimi)</h3>
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <label class="form-label required">Gönderim Yöntemi</label>
                <select class="form-select" name="mail_mailer" data-mail-mailer>
                    <option value="log" @selected($mailer === 'log')>E-posta gönderme (yalnızca günlüğe yaz)</option>
                    <option value="smtp" @selected($mailer === 'smtp')>SMTP sunucusu</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label required">Gönderen E-posta</label>
                <input class="form-control" type="email" name="mail_from_address" value="{{ old('mail_from_address', $values['mail_from_address']) }}" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label required">Gönderen Adı</label>
                <input class="form-control" name="mail_from_name" value="{{ old('mail_from_name', $values['mail_from_name']) }}" required>
            </div>
            <div class="col-12" data-mail-panel @if ($mailer !== 'smtp') hidden @endif>
                <div class="row g-3">
                    <div class="col-12 col-md-5">
                        <label class="form-label required">SMTP Sunucusu</label>
                        <input class="form-control" name="mail_host" value="{{ old('mail_host', $values['mail_host']) }}" placeholder="smtp.example.com">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label required">Port</label>
                        <input class="form-control" type="number" name="mail_port" value="{{ old('mail_port', $values['mail_port']) }}">
                    </div>
                    <div class="col-6 col-md-5">
                        <label class="form-label">Şifreleme</label>
                        <select class="form-select" name="mail_scheme">
                            <option value="none" @selected(old('mail_scheme', $values['mail_scheme']) === 'none')>Otomatik / STARTTLS (587, 25)</option>
                            <option value="smtps" @selected(old('mail_scheme', $values['mail_scheme']) === 'smtps')>SSL/TLS (465)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Kullanıcı Adı</label>
                        <input class="form-control" name="mail_username" value="{{ old('mail_username', $values['mail_username']) }}" autocomplete="off">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Şifre</label>
                        <input class="form-control" type="password" name="mail_password" autocomplete="new-password" placeholder="{{ $hasMailPassword ? '•••••••• (kayıtlı; değiştirmek için yazın)' : '' }}">
                    </div>
                    <div class="col-12">
                        <div class="installer-test">
                            <input class="form-control installer-test-input" type="email" name="test_email" placeholder="Test e-postasının gideceği adres">
                            <button type="button" class="btn btn-outline-primary" data-mail-test><x-icon name="send" class="me-1" /> Test E-postası Gönder</button>
                            <span class="installer-test-result" data-test-result role="status"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h3 class="installer-section-title"><x-icon name="brand-whatsapp" class="me-1" /> WhatsApp API</h3>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="whatsapp_enabled" value="1" @checked($whatsapp) data-whatsapp-toggle>
                    <span class="form-check-label fw-bold">WhatsApp entegrasyonunu etkinleştir</span>
                </label>
            </div>
            <div class="col-12" data-whatsapp-panel @unless ($whatsapp) hidden @endunless>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">API URL</label>
                        <input class="form-control" name="whatsapp_api_url" value="{{ old('whatsapp_api_url', $values['whatsapp_api_url']) }}" placeholder="https://api.whatsapp.example.com/v1/messages">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Oturum / Cihaz Kimliği</label>
                        <input class="form-control" name="whatsapp_session_id" value="{{ old('whatsapp_session_id', $values['whatsapp_session_id']) }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">API Token</label>
                        <input class="form-control" type="password" name="whatsapp_token" autocomplete="new-password" placeholder="{{ $hasWhatsappToken ? '•••••••• (kayıtlı; değiştirmek için yazın)' : '' }}">
                        <div class="form-hint">Token şifrelenerek saklanır.</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Group ID</label>
                        <input class="form-control" name="whatsapp_group_id" value="{{ old('whatsapp_group_id', $values['whatsapp_group_id']) }}" placeholder="120363024823902345@g.us">
                    </div>
                </div>
            </div>
        </div>
        @include('installer.partials.nav')
    </form>
@endsection
