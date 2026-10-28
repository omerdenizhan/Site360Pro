@extends('installer.layout')
@section('title', 'Yönetici')
@section('heading', 'Yönetici Hesabı')

@section('content')
    <p class="text-secondary">Sisteme tam yetkiyle girecek ilk yönetici hesabını oluşturun. Varsayılan bir şifre kullanılmaz.</p>
    <form method="post" action="{{ route('install.store', ['step' => 'admin']) }}" data-installer-form>
        @csrf
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label required">Adı Soyadı</label>
                <input class="form-control" name="name" value="{{ old('name', $values['name']) }}" autocomplete="off" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label required">E-posta (giriş adı)</label>
                <input class="form-control" type="email" name="email" value="{{ old('email', $values['email']) }}" autocomplete="off" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label @unless ($hasPassword) required @endunless">Şifre</label>
                <div class="input-group">
                    <input class="form-control" type="password" name="password" minlength="8" maxlength="72" autocomplete="new-password" data-password-input @unless ($hasPassword) required @endunless>
                    <button class="btn btn-outline-secondary" type="button" data-toggle-password title="Şifreyi göster/gizle"><x-icon name="eye" /></button>
                    <button class="btn btn-outline-secondary" type="button" data-generate-password title="Güçlü şifre üret"><x-icon name="wand" /></button>
                </div>
                <div class="installer-strength" data-strength><span></span></div>
                <div class="form-hint" data-strength-text>@if ($hasPassword) Şifre daha önce belirlendi; değiştirmek istemiyorsanız boş bırakın. @else En az 8 karakter; harf ve rakam içermelidir. @endif</div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label @unless ($hasPassword) required @endunless">Şifre Tekrarı</label>
                <input class="form-control" type="password" name="password_confirmation" maxlength="72" autocomplete="new-password" data-password-confirm @unless ($hasPassword) required @endunless>
            </div>
            <div class="col-12">
                <div class="alert alert-info mb-0"><x-icon name="info-circle" class="me-2" />Güvenlik sorusu, "Şifremi Unuttum" ekranında hesabınızı kurtarmanın tek yoludur. Cevabı unutmayacağınız bir şey seçin.</div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label required">Güvenlik Sorusu</label>
                <select class="form-select" name="security_question" required>
                    <option value="">Seçiniz</option>
                    @foreach ($questions as $question)
                        <option value="{{ $question }}" @selected(old('security_question', $values['security_question']) === $question)>{{ $question }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label required">Cevap</label>
                <input class="form-control" name="security_answer" value="{{ old('security_answer', $values['security_answer']) }}" autocomplete="off" required>
            </div>
        </div>
        @include('installer.partials.nav')
    </form>
@endsection
