@extends('installer.layout')
@section('title', 'Veritabanı')
@section('heading', 'Veritabanı Bağlantısı')

@php
    $driver = old('driver', $values['driver']);
    $isSqlite = $driver === 'sqlite';
@endphp

@section('content')
    <form method="post" action="{{ route('install.store', ['step' => 'database']) }}" data-installer-form data-test-url="{{ route('install.database.test') }}" data-busy-text="Veritabanı hazırlanıyor ve tablolar oluşturuluyor...">
        @csrf
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label required">Veritabanı Türü</label>
                <select class="form-select" name="driver" data-driver-select>
                    @foreach ($drivers as $key => $option)
                        <option value="{{ $key }}" @selected($driver === $key) @disabled(! $option['available'])>{{ $option['label'] }}@unless ($option['available']) (PHP eklentisi yok)@endunless</option>
                    @endforeach
                </select>
                <div class="form-hint">Tek sunucuda çalışan küçük ve orta ölçekli siteler için SQLite yeterlidir; kurulum gerektirmez.</div>
            </div>
            <div class="col-12" data-driver-panel="sqlite" @unless ($isSqlite) hidden @endunless>
                <div class="alert alert-info mb-0"><x-icon name="info-circle" class="me-2" />Veriler <code>database/database.sqlite</code> dosyasında tutulur. Dosya yoksa otomatik oluşturulur. Düzenli yedek almayı unutmayın (Ayarlar &rarr; Veritabanı).</div>
            </div>
            <div class="col-12" data-driver-panel="server" @if ($isSqlite) hidden @endif>
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label required">Sunucu Adresi</label>
                        <input class="form-control" name="host" value="{{ old('host', $values['host']) }}" placeholder="127.0.0.1" autocomplete="off">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label required">Port</label>
                        <input class="form-control" type="number" name="port" value="{{ old('port', $values['port']) }}" min="1" max="65535">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label required">Veritabanı Adı</label>
                        <input class="form-control" name="database" value="{{ old('database', $values['database']) }}" autocomplete="off">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label required">Kullanıcı Adı</label>
                        <input class="form-control" name="username" value="{{ old('username', $values['username']) }}" autocomplete="off">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Şifre</label>
                        <input class="form-control" type="password" name="password" autocomplete="new-password">
                    </div>
                    <div class="col-12">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" name="create_database" value="1" @checked(old('create_database'))>
                            <span class="form-check-label">Veritabanı yoksa oluşturmayı dene</span>
                        </label>
                        <div class="form-hint">Kullanıcının CREATE DATABASE yetkisi gerekir. Paylaşımlı barındırmada veritabanını önce panelden oluşturun.</div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="installer-test">
                    <button type="button" class="btn btn-outline-primary" data-connection-test><x-icon name="plug-connected" class="me-1" /> Bağlantıyı Test Et</button>
                    <span class="installer-test-result" data-test-result role="status"></span>
                </div>
            </div>
            <div class="col-12">
                <details class="installer-advanced" @if (old('fresh')) open @endif>
                    <summary><x-icon name="alert-triangle" class="me-1" /> Gelişmiş: temiz kurulum</summary>
                    <div class="mt-3">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" name="fresh" value="1" @checked(old('fresh')) data-fresh-toggle>
                            <span class="form-check-label">Mevcut tüm tabloları silip baştan oluştur</span>
                        </label>
                        <div class="form-hint text-danger">Seçilen veritabanındaki tüm tablolar ve veriler kalıcı olarak silinir. Varsayılan olarak mevcut tablolar korunur, yalnızca eksik olanlar eklenir.</div>
                        <div class="mt-2" data-fresh-panel @unless (old('fresh')) hidden @endunless>
                            <input class="form-control" name="fresh_confirmation" placeholder="Onay için TEMIZ_KURULUM yazın" autocomplete="off">
                        </div>
                    </div>
                </details>
            </div>
        </div>
        @include('installer.partials.nav', ['label' => 'Tabloları Oluştur ve Devam Et'])
    </form>
@endsection
