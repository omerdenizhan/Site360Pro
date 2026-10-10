@extends('layouts.app', ['title' => 'Ayarlar - Site360Pro'])

@php
    $tabs = [
        'general' => ['label' => 'Genel', 'icon' => 'settings-cog'],
        'whatsapp' => ['label' => 'WhatsApp API', 'icon' => 'brand-whatsapp'],
        'visual' => ['label' => 'Görsellik', 'icon' => 'palette'],
        'database' => ['label' => 'Veritabanı', 'icon' => 'database'],
    ];

    $accentColors = [
        ['code' => '#eab308', 'name' => 'Sarı'],
        ['code' => '#f59e0b', 'name' => 'Kehribar Sarısı'],
        ['code' => '#ea580c', 'name' => 'Turuncu'],
        ['code' => '#d63939', 'name' => 'Kırmızı'],
        ['code' => '#e11d48', 'name' => 'Gül Kırmızısı'],
        ['code' => '#d946ef', 'name' => 'Fuşya (Pembe)'],
        ['code' => '#9333ea', 'name' => 'Mor'],
        ['code' => '#6366f1', 'name' => 'Çivit Moru'],
        ['code' => '#206bc4', 'name' => 'Klasik Mavi'],
        ['code' => '#0284c7', 'name' => 'Okyanus Mavisi'],
        ['code' => '#00bfff', 'name' => 'Gök Mavisi'],
        ['code' => '#0d9488', 'name' => 'Turkuaz'],
        ['code' => '#2fb344', 'name' => 'Yeşil'],
        ['code' => '#10b981', 'name' => 'Zümrüt Yeşili'],
        ['code' => '#64748b', 'name' => 'Gri'],
    ];
@endphp

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-3 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Sistem</div>
                    <h2 class="page-title">Ayarlar</h2>
                    <div class="text-secondary mt-1">Sistem genel ayarları, WhatsApp API bağlantısı, görsellik ve veritabanı yedekleme işlemlerini buradan yönetin.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (session('status'))
                <div class="alert alert-success"><x-icon name="circle-check" class="me-2" />{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger"><x-icon name="alert-circle" class="me-2" />{{ $errors->first() }}</div>
            @endif
            <div class="panel-tabs mb-3" role="tablist" aria-label="Ayar sekmeleri">
                @foreach ($tabs as $key => $item)
                    <a class="panel-tab {{ $tab === $key ? 'active' : '' }}" href="{{ route('settings.index', ['tab' => $key]) }}" @if ($tab === $key) aria-current="page" @endif>
                        <x-icon :name="$item['icon']" /> {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
            <div class="card">
                @if ($tab === 'whatsapp')
                    <form method="post" action="{{ route('settings.whatsapp') }}" id="whatsappSettingsForm" novalidate data-token-saved="{{ $settings['whatsapp_token_masked'] ? '1' : '0' }}">
                        @csrf
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6 col-md-6 col-lg-6">
                                    <label class="form-label">WhatsApp API URL (Endpoint Adresi)</label>
                                    <input class="form-control" type="url" name="api_url" value="{{ old('api_url', $settings['whatsapp_api_url']) }}" placeholder="https://api.whatsapp.example.com/v1/messages">
                                    <div class="form-hint">Mesaj isteklerinin yönlendirileceği API sunucu adresi.</div>
                                </div>
                                <div class="col-6 col-md-6 col-lg-6">
                                    <label class="form-label">Oturum / Cihaz Kimliği</label>
                                    <input class="form-control" name="session_id" value="{{ old('session_id', $settings['whatsapp_session_id']) }}" placeholder="default-session-01">
                                    <div class="form-hint">Bağlı telefon/cihaz oturum parametresi.</div>
                                </div>
                                <div class="col-6 col-md-6 col-lg-6">
                                    <label class="form-label">API Token (Erişim Anahtarı)</label>
                                    <input class="form-control" type="password" name="token" placeholder="{{ $settings['whatsapp_token_masked'] ?? 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...' }}">
                                    <div class="form-hint">Boş bırakırsanız mevcut token korunur. Token güvenli şifrelenir.</div>
                                </div>
                                <div class="col-6 col-md-6 col-lg-6">
                                    <label class="form-label">Group ID (Hedef Grup Kimliği)</label>
                                    <input class="form-control" name="group_id" value="{{ old('group_id', $settings['whatsapp_group_id']) }}" placeholder="120363024823902345@g.us">
                                    <div class="form-hint">Duyuruların paylaşılacağı site/apartman WhatsApp grup ID'si.</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="enabled" value="1" @checked(old('enabled', $settings['whatsapp_enabled']) === '1')>
                                        <span class="form-check-label fw-bold">WhatsApp Entegrasyonu Aktif</span>
                                    </label>
                                    <div class="form-hint">Aktif edildiğinde duyurular grup üzerinden otomatik yayınlanabilir.</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-primary" type="submit"><x-icon name="device-floppy" class="me-1" /> Kaydet</button>
                        </div>
                    </form>
                @elseif ($tab === 'visual')
                    <form method="post" action="{{ route('settings.visual') }}">
                        @csrf
                        <div class="card-header"><h3 class="card-title"><x-icon name="palette" class="me-1" /> Arayüz Efektleri</h3></div>
                        <div class="card-body">
                            <div class="row g-4 align-items-center">
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-label fw-bold">Vurgu Rengi</label>
                                    @php
                                        $currentAccent = old('accent_color', $settings['accent_color']);
                                        $currentAccentItem = collect($accentColors)->firstWhere('code', $currentAccent)
                                            ?? ['code' => $currentAccent, 'name' => 'Özel Renk'];
                                    @endphp
                                    <input type="hidden" name="accent_color" id="accentColorInput" value="{{ $currentAccentItem['code'] }}">
                                    <div class="accent-picker" id="accentPicker">
                                        <button type="button" class="form-select accent-picker-btn" id="accentPickerBtn" aria-haspopup="listbox" aria-expanded="false">
                                            <span class="accent-swatch" id="accentPickerSwatch" style="background-color: {{ $currentAccentItem['code'] }};"></span>
                                            <span class="accent-picker-text" id="accentPickerText">{{ $currentAccentItem['name'] }} <span class="text-secondary">({{ $currentAccentItem['code'] }})</span></span>
                                        </button>
                                        <ul class="accent-picker-list" id="accentPickerList" role="listbox" hidden>
                                            @foreach ($accentColors as $color)
                                                <li role="option" tabindex="0" class="accent-picker-item {{ $currentAccentItem['code'] === $color['code'] ? 'is-selected' : '' }}" data-code="{{ $color['code'] }}" data-name="{{ $color['name'] }}" aria-selected="{{ $currentAccentItem['code'] === $color['code'] ? 'true' : 'false' }}">
                                                    <span class="accent-swatch" style="background-color: {{ $color['code'] }};"></span>
                                                    <span class="accent-picker-text">{{ $color['name'] }} <span class="text-secondary">({{ $color['code'] }})</span></span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="form-hint mt-2">
                                        Tüm projedeki butonlar, sekmeler ve vurgulu alanların temasını belirler.
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="glass_effect" value="1" @checked(old('glass_effect', $settings['glass_effect']) === '1')>
                                        <span class="form-check-label fw-bold">Cam (Bulanıklılık Efekti)</span>
                                    </label>
                                    <div class="text-secondary small mt-1" style="font-size: 0.8rem;">
                                        Panellerde ve kartlarda modern buzlu cam (glassmorphism) ve bulanıklık efekti sağlar.
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="animations_effect" value="1" @checked(old('animations_effect', $settings['animations_effect']) === '1')>
                                        <span class="form-check-label fw-bold">Geçiş ve Çubuk Animasyonları</span>
                                    </label>
                                    <div class="text-secondary small mt-1" style="font-size: 0.8rem;">
                                        Sayfa bileşenlerinde, çubuklarda ve geçişlerde akıcı hareketli animasyonlar sağlar.
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="card_hover_effect" value="1" @checked(old('card_hover_effect', $settings['card_hover_effect']) === '1')>
                                        <span class="form-check-label fw-bold">Kart Hover Animasyonları</span>
                                    </label>
                                    <div class="text-secondary small mt-1" style="font-size: 0.8rem;">
                                        kartların içerisinde kullanılan renk ne ise hover efektinin de aynı renk olmasını sağlar
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header border-top"><h3 class="card-title"><x-icon name="sparkles" class="me-1" /> Baloncuk Efektleri</h3></div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="enabled" value="1" @checked(old('enabled', $settings['bubble_enabled']) === '1')>
                                        <span class="form-check-label fw-bold fs-3">Baloncuk Efektini Etkinleştir</span>
                                    </label>
                                    <div class="text-secondary small mt-1">Arka planda süzülen interaktif ve modern baloncuk parçacıkları animasyonunu aktif eder.</div>
                                </div>
                                <div class="col-12">
                                    <div class="fx-grid">
                                        <div class="fx-row">
                                            <label class="form-label fw-bold fx-label">Baloncuk Sayısı</label>
                                            <select class="form-select fx-control" name="count">
                                                <option value="10" @selected(old('count', $settings['bubble_count']) == '10')>10 Adet (Hafif)</option>
                                                <option value="20" @selected(old('count', $settings['bubble_count']) == '20')>20 Adet (Önerilen)</option>
                                                <option value="35" @selected(old('count', $settings['bubble_count']) == '35')>35 Adet (Yoğun)</option>
                                                <option value="50" @selected(old('count', $settings['bubble_count']) == '50')>50 Adet (Maksimum)</option>
                                            </select>
                                            <span class="form-hint fx-hint">Ekranda aynı anda hareket edecek baloncuk adedi.</span>
                                        </div>
                                        <div class="fx-row">
                                            <label class="form-label fw-bold fx-label">Baloncuk Hızı</label>
                                            <select class="form-select fx-control" name="speed">
                                                <option value="slow" @selected(old('speed', $settings['bubble_speed']) === 'slow')>Yavaş / Dingin</option>
                                                <option value="normal" @selected(old('speed', $settings['bubble_speed']) === 'normal')>Normal Hız</option>
                                                <option value="fast" @selected(old('speed', $settings['bubble_speed']) === 'fast')>Hızlı / Dinamik</option>
                                            </select>
                                            <span class="form-hint fx-hint">Baloncukların yukarıya doğru akış hızı.</span>
                                        </div>
                                        <div class="fx-row">
                                            <label class="form-label fw-bold fx-label">Baloncuk Şeffaflığı / Opaklık</label>
                                            <select class="form-select fx-control" name="opacity">
                                                <option value="15" @selected(old('opacity', $settings['bubble_opacity']) == '15')>%15 (Çok Hafif & Zarif)</option>
                                                <option value="30" @selected(old('opacity', $settings['bubble_opacity']) == '30')>%30 (Dengeli Görünüm)</option>
                                                <option value="50" @selected(old('opacity', $settings['bubble_opacity']) == '50')>%50 (Canlı & Belirgin)</option>
                                            </select>
                                            <span class="form-hint fx-hint">Baloncukların arka plandaki görünürlük derecesi.</span>
                                        </div>
                                        <div class="fx-row">
                                            <label class="form-label fw-bold fx-label">Renk Modu</label>
                                            <select class="form-select fx-control" name="color_mode">
                                                <option value="accent" @selected(old('color_mode', $settings['bubble_color_mode']) === 'accent')>Seçili Vurgu Rengine Göre</option>
                                                <option value="blue" @selected(old('color_mode', $settings['bubble_color_mode']) === 'blue')>Gökyüzü Mavi Tonları</option>
                                                <option value="pastel" @selected(old('color_mode', $settings['bubble_color_mode']) === 'pastel')>Pastel Renkler</option>
                                                <option value="white" @selected(old('color_mode', $settings['bubble_color_mode']) === 'white')>Buzlu Beyaz / Pastel</option>
                                            </select>
                                            <span class="form-hint fx-hint">Baloncukların dolgu ve parlama renk düzeni.</span>
                                        </div>
                                        <div class="fx-row">
                                            <label class="form-label fw-bold fx-label">Baloncuk Boyutu</label>
                                            <select class="form-select fx-control" name="size">
                                                <option value="small" @selected(old('size', $settings['bubble_size']) === 'small')>Küçük Baloncuklar</option>
                                                <option value="medium" @selected(old('size', $settings['bubble_size']) === 'medium')>Orta Boyutlu</option>
                                                <option value="large" @selected(old('size', $settings['bubble_size']) === 'large')>Büyük Baloncuklar</option>
                                                <option value="mixed" @selected(old('size', $settings['bubble_size']) === 'mixed')>Karışık (Doğal Derinlik)</option>
                                            </select>
                                            <span class="form-hint fx-hint">Baloncuk çaplarının dağılımı.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-primary" type="submit"><x-icon name="device-floppy" class="me-1" /> Kaydet</button>
                        </div>
                    </form>
                @elseif ($tab === 'database')
                    @if (! $isAdmin)
                        <div class="card-body">
                            <div class="alert alert-warning mb-0"><x-icon name="lock" class="me-2" />Veritabanı yedekleme ve geri yükleme işlemleri yalnızca genel yöneticiler tarafından yapılabilir.</div>
                        </div>
                    @else
                        <div class="card-header"><h3 class="card-title"><x-icon name="database" class="me-1" /> Veritabanı Yönetimi</h3></div>
                        <div class="card-body">
                            <div class="row g-3 mb-1">
                                <div class="col-sm-6 col-xl">
                                    <div class="card position-relative" style="min-height: auto !important; height: max-content;">
                                        <div class="position-absolute top-0 end-0 p-3">
                                            <span class="avatar bg-blue-lt"><x-icon name="database" /></span>
                                        </div>
                                        <div class="card-body py-3 px-3 d-flex flex-column justify-content-center text-center">
                                            <div class="text-secondary fw-bold fs-5 m-1 lh-1" style="color: #206bc4 !important;">Sürücü</div>
                                            <div class="h1 fw-bold my-1 lh-1" style="color: #206bc4 !important; -webkit-text-fill-color: #206bc4 !important; background: none !important;">
                                                {{ strtoupper($database['driver']) }}
                                            </div>
                                            <div class="text-secondary fw-semibold m-0 lh-1" style="color: #206bc4 !important;">Aktif bağlantı</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl">
                                    <div class="card position-relative" style="min-height: auto !important; height: max-content;">
                                        <div class="position-absolute top-0 end-0 p-3">
                                            <span class="avatar bg-purple-lt"><x-icon name="table" /></span>
                                        </div>
                                        <div class="card-body py-3 px-3 d-flex flex-column justify-content-center text-center">
                                            <div class="text-secondary fw-bold fs-5 m-1 lh-1" style="color: #6f42c1 !important;">Tablo Sayısı</div>
                                            <div class="h1 fw-bold my-1 lh-1" style="color: #6f42c1 !important; -webkit-text-fill-color: #6f42c1 !important; background: none !important;">
                                                {{ $database['table_count'] }}
                                            </div>
                                            <div class="text-secondary fw-semibold m-0 lh-1" style="color: #6f42c1 !important;">Mevcut tablolar</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl">
                                    <div class="card position-relative" style="min-height: auto !important; height: max-content;">
                                        <div class="position-absolute top-0 end-0 p-3">
                                            <span class="avatar bg-azure-lt"><x-icon name="server" /></span>
                                        </div>
                                        <div class="card-body py-3 px-3 d-flex flex-column justify-content-center text-center">
                                            <div class="text-secondary fw-bold fs-5 m-1 lh-1" style="color: #0ea5e9 !important;">Toplam Kayıt</div>
                                            <div class="h1 fw-bold my-1 lh-1" style="color: #0ea5e9 !important; -webkit-text-fill-color: #0ea5e9 !important; background: none !important;">
                                                {{ number_format($database['row_count'], 0, ',', '.') }}
                                            </div>
                                            <div class="text-secondary fw-semibold m-0 lh-1" style="color: #0ea5e9 !important;">Toplam satır</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row g-4">
                                <div class="col-4 col-md-4 col-lg-4">
                                    <h4 class="mb-1"><x-icon name="download" class="me-1" /> Yedekle</h4>
                                    <p class="text-secondary small">Tüm tablolardaki kayıtların anlık yedeğini bilgisayarınıza indirin. Geri yükleme için JSON yedeği kullanılmalıdır.</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a class="btn btn-primary" href="{{ route('settings.database.json') }}"><x-icon name="file-code" class="me-1" /> JSON Yedeği İndir</a>
                                        <a class="btn btn-outline-primary" href="{{ route('settings.database.sql') }}"><x-icon name="file-text" class="me-1" /> SQL Yedeği İndir</a>
                                    </div>
                                </div>
                                <div class="col-4 col-md-4 col-lg-4">
                                    <h4 class="mb-1"><x-icon name="upload" class="me-1" /> Geri Yükle</h4>
                                    <p class="text-secondary small">Daha önce aldığınız Site360Pro JSON yedeğini seçin. Mevcut kayıtların yerine yedekteki kayıtlar yazılır.</p>
                                    <form method="post" action="{{ route('settings.database.restore') }}" enctype="multipart/form-data" data-confirm="Mevcut veriler yedek dosyasındaki verilerle değiştirilecek. Devam edilsin mi?">
                                        @csrf
                                        <div class="mb-2">
                                            <input class="form-control" type="file" name="backup" accept=".json,application/json" required>
                                        </div>
                                        <div class="mb-2">
                                            <input class="form-control" name="confirmation" placeholder="Onay için GERI_YUKLE yazın" autocomplete="off" required>
                                        </div>
                                        <button class="btn btn-warning" type="submit"><x-icon name="database-import" class="me-1" /> Yedekten Geri Yükle</button>
                                    </form>
                                </div>
                                <div class="col-4 col-md-4 col-lg-4">
                                    <h4 class="mb-1 text-danger"><x-icon name="trash" class="me-1" /> Verileri Sıfırla</h4>
                                    <p class="text-secondary small">Tüm tablolardaki kayıtları siler (kullanıcılar dahil). Şema ve migration geçmişi korunur. Bu işlem geri alınamaz; önce yedek alın.</p>
                                    <form method="post" action="{{ route('settings.database.wipe') }}" data-confirm="Tüm kayıtlar kalıcı olarak silinecek. Devam edilsin mi?">
                                        @csrf
                                        <div class="mb-2">
                                            <input class="form-control" name="confirmation" placeholder="Onay için VERITABANINI_SIL yazın" autocomplete="off" required>
                                        </div>
                                        <button class="btn btn-danger" type="submit"><x-icon name="database-off" class="me-1" /> Tüm Kayıtları Sil</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <form method="post" action="{{ route('settings.general') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-6 col-lg-6">
                                    <label class="form-label">Genel Erişim Adresi</label>
                                    <input class="form-control" name="public_url" value="{{ old('public_url', $settings['app_public_url']) }}" placeholder="https://panel.siteadi.com">
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <label class="form-label">Destek E-posta</label>
                                    <input class="form-control" type="email" name="support_email" value="{{ old('support_email', $settings['support_email']) }}" placeholder="destek@example.com">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-primary" type="submit"><x-icon name="device-floppy" class="me-1" /> Kaydet</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
