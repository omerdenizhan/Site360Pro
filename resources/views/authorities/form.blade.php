<div class="card-header">
    <h3 class="card-title">Hesap Bilgileri</h3>
</div>
<div class="card-body">
    @if ($errors->any())
        <div class="alert alert-danger"><x-icon name="alert-circle" class="me-2" />{{ $errors->first() }}</div>
    @endif

    <div class="row g-3 align-items-start">
        <div class="col-3 col-md-3 col-lg-3">
            <label class="form-label">Adı Soyadı</label>
            <input class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="col-3 col-md-3 col-lg-3">
            <label class="form-label">E-posta</label>
            <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="col-3 col-md-3 col-lg-3">
            <label class="form-label">Rol</label>
            <select class="form-select" name="role">
                <option value="site_manager" @selected(old('role', $user->role) === 'site_manager')>Site yöneticisi</option>
                <option value="admin" @selected(old('role', $user->role) === 'admin')>Genel yönetici</option>
            </select>
            <div class="form-hint">Genel yönetici tüm siteleri görebilir. Site yöneticisi seçilen site için çalışır.</div>
        </div>
        <div class="col-3 col-md-3 col-lg-3">
            <label class="form-label">Site</label>
            <select class="form-select" name="site_id">
                <option value="">Tüm siteler / genel yönetici</option>
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}" @selected((string) old('site_id', $user->site_id) === (string) $site->id)>{{ $site->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-3 col-md-3 col-lg-3">
            <label class="form-label">Şifre</label>
            <input class="form-control" type="password" name="password" @if (! $user->exists) required @endif>
            @if ($user->exists)
                <div class="form-hint">Değiştirmek istemiyorsanız boş bırakın.</div>
            @endif
        </div>
        <div class="col-3 col-md-3 col-lg-3">
            <label class="form-label">Şifre Tekrar</label>
            <input class="form-control" type="password" name="password_confirmation" @if (! $user->exists) required @endif>
        </div>
        <div class="col-3 col-md-3 col-lg-3">
            <label class="form-label d-none d-lg-block">&nbsp;</label>
            <div class="d-flex align-items-center" style="min-height: 36px;">
                <label class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))>
                    <span class="form-check-label">Hesap aktif</span>
                </label>
            </div>
        </div>
        <div class="col-12 mt-4">
            <h4 class="text-primary mb-1"><x-icon name="shield-lock" class="me-1" /> Şifre Kurtarma Seçenekleri</h4>
            <div class="text-secondary small">Kullanıcının şifresini unutması durumunda doğrulama yapılacak güvenlik sorusu ve cevabı.</div>
        </div>
        <div class="col-6 col-md-6 col-lg-6">
            <label class="form-label">Güvenlik Sorusu</label>
            @php
                $selectedQuestion = old('security_question', $user->security_question);
                $questionOptions = \App\Models\User::SECURITY_QUESTIONS;

                if (filled($selectedQuestion) && ! in_array($selectedQuestion, $questionOptions, true)) {
                    array_unshift($questionOptions, $selectedQuestion);
                }
            @endphp
            <select class="form-select" name="security_question">
                <option value="">-- Güvenlik sorusu seçiniz --</option>
                @foreach ($questionOptions as $question)
                    <option value="{{ $question }}" @selected($selectedQuestion === $question)>{{ $question }}</option>
                @endforeach
            </select>
            <div class="form-hint">Şifre sıfırlama talebinde kullanıcıya bu soru sorulacaktır.</div>
        </div>
        <div class="col-6 col-md-6 col-lg-6">
            <label class="form-label">Güvenlik Sorusu Cevabı</label>
            <input class="form-control" name="security_answer" value="{{ old('security_answer', $user->security_answer) }}" placeholder="Cevabınızı giriniz">
            <div class="form-hint">Cevap doğrulanarak yeni şifre belirlenmesi sağlanır.</div>
        </div>
    </div>
</div>

<div class="card-footer text-end">
    <a class="btn" href="{{ route('authorities.index') }}">Vazgeç</a>
    <button class="btn btn-primary" type="submit"><x-icon name="check" class="me-1" /> {{ $button }}</button>
</div>
