<!doctype html>
<html lang="tr" data-bs-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Kurulum') - Site360Pro Kurulum Sihirbazı</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @if (is_file(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="installer-body">
    <div id="appToastContainer" role="region" aria-live="polite" aria-label="Bildirimler"></div>
    <div class="position-fixed top-0 end-0 p-3 installer-theme">
        <button type="button" class="btn btn-icon btn-ghost-secondary theme-toggle-btn shadow-sm bg-body" data-theme-toggle aria-label="Karanlık Mod" title="Temayı Değiştir">
            <span class="theme-icon-light"><x-icon name="moon" /></span>
            <span class="theme-icon-dark"><x-icon name="sun" /></span>
        </button>
    </div>
    <div class="installer-shell">
        <header class="installer-header">
            <img src="{{ asset('favicon.ico') }}" alt="Site360Pro">
            <div>
                <h1>Site360Pro Kurulum Sihirbazı</h1>
                <p>Birkaç adımda sisteminizi kullanıma hazırlayın.</p>
            </div>
        </header>
        <div class="installer-layout">
            @isset($steps)
                <nav class="installer-tabs" aria-label="Kurulum adımları">
                    <ol>
                        @foreach ($steps as $item)
                            <li class="installer-tab {{ $item['current'] ? 'is-current' : '' }} {{ $item['done'] ? 'is-done' : '' }}" @if ($item['current']) aria-current="step" @endif>
                                <span class="installer-tab-mark">
                                    <x-icon :name="$item['done'] ? 'circle-check' : $item['icon']" />
                                </span>
                                <span class="installer-tab-label">{{ $item['label'] }}</span>
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endisset
            <main class="installer-main card">
                @hasSection('heading')
                    <div class="card-header installer-card-header">
                        <div>
                            @isset($number)
                                <div class="page-pretitle">Adım {{ $number }} / {{ $total }}</div>
                            @endisset
                            <h2 class="card-title">@yield('heading')</h2>
                        </div>
                    </div>
                @endif
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <div class="d-flex">
                                <div><x-icon name="alert-circle" class="icon alert-icon" /></div>
                                <div>
                                    @foreach ($errors->all() as $message)
                                        <div>{{ $message }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
        <footer class="installer-footer">Site360Pro &middot; Kurulum tamamlandığında bu sihirbaz otomatik olarak kapatılır.</footer>
    </div>
    <div class="installer-busy" id="installerBusy" hidden>
        <div class="installer-busy-box">
            <span class="spinner-border" role="status" aria-hidden="true"></span>
            <div id="installerBusyText">İşlem yapılıyor, lütfen bekleyin...</div>
        </div>
    </div>
</body>
</html>
