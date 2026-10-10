<!doctype html>
<html lang="tr" data-bs-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sisteme Giriş - Site360Pro</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
        <button type="button" class="btn btn-icon btn-ghost-secondary theme-toggle-btn shadow-sm bg-body" data-theme-toggle aria-label="Karanlık Mod" title="Temayı Değiştir">
            <span class="theme-icon-light"><x-icon name="moon" /></span>
            <span class="theme-icon-dark"><x-icon name="sun" /></span>
        </button>
    </div>


    <div class="login-split">
        <section class="login-hero">
            <a class="hero-brand" href="{{ route('login') }}">
                <img src="{{ asset('favicon.ico') }}" alt="Site360Pro">
                <span>Site360Pro</span>
            </a>

            <svg class="hero-art" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <rect x="20" y="120" width="90" height="180" rx="6" style="fill: var(--art-a)"/>
                <rect x="125" y="50" width="120" height="250" rx="8" style="fill: var(--art-b)"/>
                <rect x="260" y="95" width="105" height="205" rx="6" style="fill: var(--art-a)"/>
                <g style="fill: var(--art-win)">
                    <rect x="142" y="72" width="22" height="22" rx="3"/><rect x="178" y="72" width="22" height="22" rx="3"/><rect x="214" y="72" width="16" height="22" rx="3"/>
                    <rect x="142" y="112" width="22" height="22" rx="3"/><rect x="178" y="112" width="22" height="22" rx="3" style="fill: var(--art-lit)"/><rect x="214" y="112" width="16" height="22" rx="3"/>
                    <rect x="142" y="152" width="22" height="22" rx="3" style="fill: var(--art-lit)"/><rect x="178" y="152" width="22" height="22" rx="3"/><rect x="214" y="152" width="16" height="22" rx="3" style="fill: var(--art-lit)"/>
                    <rect x="142" y="192" width="22" height="22" rx="3"/><rect x="178" y="192" width="22" height="22" rx="3"/><rect x="214" y="192" width="16" height="22" rx="3"/>
                </g>
                <g style="fill: var(--art-win); opacity: .6">
                    <rect x="36" y="142" width="16" height="16" rx="2"/><rect x="66" y="142" width="16" height="16" rx="2"/>
                    <rect x="36" y="176" width="16" height="16" rx="2"/><rect x="66" y="176" width="16" height="16" rx="2"/>
                    <rect x="276" y="116" width="16" height="16" rx="2"/><rect x="306" y="116" width="16" height="16" rx="2"/><rect x="336" y="116" width="16" height="16" rx="2"/>
                    <rect x="276" y="150" width="16" height="16" rx="2"/><rect x="306" y="150" width="16" height="16" rx="2"/><rect x="336" y="150" width="16" height="16" rx="2"/>
                </g>
                <rect x="165" y="250" width="40" height="50" rx="4" style="fill: var(--art-door)"/>
                <path d="M0 300h400" style="stroke: var(--art-ground)" stroke-width="2"/>
            </svg>

            <div class="hero-body">
                <span class="hero-kicker"><x-icon name="shield-check" /> Site &amp; Apartman Yönetimi</span>
                <h1 class="hero-title">Apartmanınızın tüm finansı <em>tek ekranda</em>, kontrol sizde.</h1>
                <p class="hero-lead">Aidat tahakkuku, tahsilat takibi, gelir-gider kayıtları ve sakin iletişimini tek bir güvenli panelden yönetin. Hızlı, şeffaf ve hatasız.</p>

                <div class="hero-features">
                    <div class="hero-feature" tabindex="0">
                        <span class="ico"><x-icon name="receipt" /></span>
                        <div><h4>Aidat &amp; Tahakkuk</h4><p>Dönemlik borçlandırmayı birkaç tıkla oluşturun, daire bazlı takip edin.</p></div>
                    </div>
                    <div class="hero-feature" tabindex="0">
                        <span class="ico"><x-icon name="wallet" /></span>
                        <div><h4>Tahsilat &amp; Makbuz</h4><p>Ödemeleri kaydedin, makbuzu anında PDF olarak paylaşın.</p></div>
                    </div>
                    <div class="hero-feature" tabindex="0">
                        <span class="ico"><x-icon name="building-bank" /></span>
                        <div><h4>Kasa &amp; Raporlar</h4><p>Gelir, gider ve kasa devrini aylık ve yıllık raporlarla izleyin.</p></div>
                    </div>
                    <div class="hero-feature" tabindex="0">
                        <span class="ico"><x-icon name="bell" /></span>
                        <div><h4>Duyurular</h4><p>Sakinlere önemli bilgileri hızlıca ulaştırın, iletişimi güçlendirin.</p></div>
                    </div>
                </div>
            </div>

            <div class="hero-stats">
                <div><strong>7/24</strong><span>Erişim</span></div>
                <div><strong>%100</strong><span>Şeffaf kayıt</span></div>
                <div><strong>Tek panel</strong><span>Tüm bloklar</span></div>
            </div>
        </section>

        <section class="login-panel">
            <form class="card card-md login-card" method="post" action="{{ route('login.store') }}" autocomplete="off">
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
                    <div id="loginPageSuccessAlert" class="alert alert-success d-none mb-3">
                        <x-icon name="circle-check" class="me-2" /><span></span>
                    </div>
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
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-link text-decoration-none p-0 text-primary fw-medium" id="btnOpenForgotPassword">
                            <x-icon name="key" class="me-1" style="width: 16px; height: 16px;" /> Şifremi Unuttum
                        </button>
                    </div>
                    <div class="text-secondary small mt-3 text-center">Yetkili hesabınızla güvenli oturum açın.</div>
                </div>
            </form>
        </section>
    </div>
    @include('auth.forgot-password-modal')
</body>
</html>
