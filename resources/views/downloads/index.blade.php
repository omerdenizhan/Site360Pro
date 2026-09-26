<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Script İndir - Site360Pro</title>
    <script>
        (function() {
            var theme = localStorage.getItem('SiteAptFinansYonetimi_theme') || '{{ session('theme', 'light') }}';
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
                <a class="d-inline-flex align-items-center gap-2 text-decoration-none text-body" href="{{ route('downloads') }}">
                    <span class="brand-mark"><x-icon name="building-skyscraper" class="fs-2" /></span>
                    <span class="text-start">
                        <span class="d-block fw-bold fs-2">Site360Pro</span>
                        <span class="d-block text-secondary small">Kaynak kodu paketi</span>
                    </span>
                </a>
            </div>

            <form class="card card-md" method="post" action="{{ route('downloads.download') }}" autocomplete="off">
                @csrf
                <div class="card-body">
                    <div class="mb-4">
                        <div class="badge bg-primary-lt mb-3">
                            <x-icon name="shield-lock" class="me-1" /> Korumalı indirme
                        </div>
                        <h1 class="h2 mb-1">İndirme şifresi</h1>
                        <p class="text-secondary mb-0">Site360Pro kurulum paketini indirmek için erişim şifresini girin.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <x-icon name="alert-circle" class="me-2" />{{ $errors->first() }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label" for="downloadPassword">Şifre</label>
                        <input
                            class="form-control @error('password') is-invalid @enderror"
                            id="downloadPassword"
                            type="password"
                            name="password"
                            inputmode="numeric"
                            autocomplete="current-password"
                            required
                            autofocus
                        >
                    </div>

                    <button class="btn btn-primary w-100" type="submit">
                        <x-icon name="download" class="me-1" /> Paketi İndir
                    </button>

                    <a class="btn btn-outline-secondary w-100 mt-2" href="{{ route('dashboard') }}">
                        <x-icon name="arrow-left" class="me-1" /> Ana Panele Dön
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
