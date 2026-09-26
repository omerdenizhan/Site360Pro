<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sisteme Giriş - Site360Pro</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script>
        (function() {
            var theme = localStorage.getItem('Site360Pro_theme') || '{{ session('theme', 'light') }}';
            document.documentElement.setAttribute('data-bs-theme', theme);
            document.documentElement.classList.add(theme === 'dark' ? 'theme-dark' : 'theme-light');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
        <button type="button" class="btn btn-icon btn-ghost-secondary theme-toggle-btn shadow-sm bg-body" data-theme-toggle aria-label="Karanlık Mod" title="Temayı Değiştir">
            <span class="theme-icon-light"><x-icon name="moon" /></span>
            <span class="theme-icon-dark"><x-icon name="sun" /></span>
        </button>
    </div>

    <div class="page page-center login-bg">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a class="d-inline-flex align-items-center gap-2 text-decoration-none text-body" href="{{ route('login') }}">
                    <span class="brand-mark">
                        <img src="{{ asset('favicon.ico') }}" alt="Site360Pro" style="width: 32px; height: 32px;">
                    </span>
                    <span class="text-start">
                        <span class="d-block fw-bold fs-2">Site360Pro</span>
                        <span class="d-block text-secondary small">Yönetim paneli</span>
                    </span>
                </a>
            </div>

            <form class="card card-md" method="post" action="{{ route('login.store') }}" autocomplete="off">
                @csrf

                <div class="card-body">
                    <div class="mb-4">
                        <div class="text-center mb-4">
                            <span class="badge bg-primary-lt"><x-icon name="shield-check" class="me-1" /> Güvenli erişim</span>
                        </div>
                        <h2 class="h2 mb-1 text-center">Yönetici girişi</h2>
                        <p class="text-secondary mb-0">Site/Apartman finans operasyonunu yönetmek için sisteme kayıtlı hesabınızla oturum açın.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <div class="d-flex">
                                <div><x-icon name="alert-circle" class=" icon alert-icon" /></div>
                                <div>{{ $errors->first() }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">E-posta</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Şifre</label>
                        <input class="form-control" type="password" name="password" autocomplete="current-password" required>
                    </div>

                    <button class="btn btn-primary w-100" type="submit">
                        <x-icon name="login" class=" me-1" /> Giriş Yap
                    </button>

                    <div class="text-secondary small mt-3 text-center">Yetkili hesabınızla güvenli oturum açın.</div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
