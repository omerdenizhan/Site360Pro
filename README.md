<h1 align="center">⭐ Site360Pro - Site ve Apartman Yönetim Sistemi ⭐</h1>

Site ve apartman yönetimi için geliştirilmiş, Laravel tabanlı bir **finans ve yönetim paneli**dir. Site → blok → daire yapısını, sakinleri, aidat tahakkuklarını, ödemeleri (makbuzlu), gelir-giderleri, duyuruları ve raporları tek bir yerden yönetmenizi sağlar. Arayüz Türkçedir.

## 📌 Özellikler

| Modül | Açıklama |
|---|---|
| **Ana Panel** | Seçili site/blok/dönem için tahsilat oranı, tahakkuk-tahsilat özeti, kasa bakiyesi, takip listesi, son ödemeler ve işlem günlükleri. |
| **Site Yönetimi** | Birden çok site; her sitenin blokları. |
| **Daireler** | Blok bazlı daire kayıtları ve istatistikleri. |
| **Sakinler** | Ev sahibi/kiracı, iletişim, iş yeri ve (en çok) iki araç bilgisi; PDF liste çıktısı. |
| **Aidatlar (Tahakkuk)** | Dönem (yıl/ay) bazlı, daire bazlı veya toplu aidat oluşturma; durum filtreleri. |
| **Ödemeler** | Tahsilat kaydı (banka, EFT, kart, nakit), otomatik makbuz numarası, makbuz sayfası ve PDF, WhatsApp ile makbuz paylaşımı. |
| **Gelirler / Giderler** | Site bazlı, kategorili gelir ve gider takibi. |
| **Duyurular** | Duyuru yönetimi; üst çubukta kayan duyuru bandı; WhatsApp bağlantısı ve e-posta ile gönderim. |
| **Yetkililer** | Admin ve site yöneticisi hesapları; güvenlik sorusu tanımlama; hesap kilitleme. |
| **Raporlar** | Yıllık/aylık finans raporu ve A4 PDF çıktısı. |
| **Daire Sahibi Raporu** | Daire bazlı yıllık aidat özeti sayfası ve PDF; geçici bağlantı (token) ile paylaşım. |
| **Vakıfbank Entegrasyonu** | Hesap hareketi tablosu, ayar ekranı, manuel eşleştirme/onay ve zamanlanmış kontrol komutu. |
| **Kurulum Sihirbazı** | İlk açılışta tarayıcıdan: gereksinim denetimi, .env ve veritabanı, yönetici hesabı, site/blok/daire, e-posta/WhatsApp ve görsellik ayarları. |
| **Şifremi Unuttum** | E-posta → güvenlik sorusu → yeni şifre adımlarından oluşan 3 adımlı modal akışı. |
| **Ayarlar** | Genel, WhatsApp API, Görsellik ve Veritabanı sekmeleri. |
| **Tema** | Aydınlık/karanlık mod (oturumda ve tarayıcıda saklanır). |
| **Bildirimler** | Sağ altta toast bildirimleri; silme gibi işlemlerde onay penceresi. |
| **Denetim Kaydı** | Önemli işlemler `audit_logs` tablosuna yazılır. |

## 📌 Teknoloji Yığını

- **Backend:** PHP 8.3+, Laravel 13, Eloquent ORM
- **PDF:** dompdf (`dompdf/dompdf`)
- **Ön yüz:** Blade, [Tabler](https://tabler.io) (Bootstrap 5 tabanlı), Tabler Icons
- **Derleme:** Vite, `laravel-vite-plugin`, Tailwind CSS eklentisi
- **Veritabanı:** SQLite (varsayılan), MySQL veya MariaDB (sihirbaz bu üçünü sunar)
- **Test:** PHPUnit

## 📌 Gereksinimler

- PHP **8.3+** (kurulu bağımlılık sürümleri 8.4 isteyebilir; `composer install` çıktısına bakın)
- Composer 2
- Node.js 20+ ve npm
- PHP eklentileri: `pdo` (+ `pdo_sqlite`/`pdo_mysql`/`pdo_pgsql`), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` veya `imagick`

## 📌 Kurulum

```bash
# 1) Klasöre girin
cd Site360Pro

# 2) PHP bağımlılıkları ve ön yüz derlemesi
composer install
npm install
npm run build                            # geliştirme: npm run dev

# 3) Çalıştırma (belge kökü public/ olmalıdır)
php artisan serve
```

Tarayıcıdan `http://localhost/Site360Pro/` adresini açın. Sistem henüz kurulmadıysa **Kurulum Sihirbazı** (`/install`) otomatik açılır; `.env` dosyası ve uygulama anahtarı da sihirbaz tarafından oluşturulur. Laragon/XAMPP gibi ortamlarda proje klasörünü `www` altına koyup sanal host kullanabilirsiniz.

> **Önemli:** `resources/css/app.css` veya `resources/js/app.js` değiştiğinde `npm run build` çalıştırın (ya da geliştirme sırasında `npm run dev` açık tutun). Tarayıcı, `public/build` içindeki derlenmiş dosyaları yükler; derleme yapılmazsa stil/JS değişiklikleri (sihirbazın görünümü dahil) görünmez. `php artisan cache:clear` / `optimize:clear` Vite derlemesini etkilemez.

### Kurulum Sihirbazı

Sihirbaz sekiz adımdan oluşur; her adım bir öncekini tamamlamadan açılmaz ve girdiğiniz bilgiler son adıma kadar oturumda tutulur (şifreler şifrelenmiş olarak):

| Adım | Ne yapar |
|---|---|
| 1. Gereksinimler | PHP sürümü, zorunlu/önerilen eklentiler, klasör ve `.env` yazma izinleri, ön yüz derlemesi. Denetimler iki kutuda gruplanır: **PHP, Veritabanı ve Önerilen Eklentiler** ile **Zorunlu Eklentiler ve Dosya İzinleri** (her kutuda alt başlıklar). Zorunlu madde eksikse devam edilemez. |
| 2. Uygulama | `APP_URL` (otomatik algılanır), ortam (canlı/geliştirme), hata ayıklama, saat dilimi. |
| 3. Veritabanı | SQLite / MySQL / MariaDB seçimi, **bağlantı testi**, isteğe bağlı veritabanı oluşturma, tabloların (migration) oluşturulması ve bağlantının `.env` dosyasına yazılması. Boş olmayan veritabanında mevcut tablolar korunur; "temiz kurulum" yalnızca `TEMIZ_KURULUM` yazılarak yapılabilir. Kullanıcı kaydı bulunan bir veritabanı reddedilir. |
| 4. Yönetici | Ad, e-posta, güçlü şifre (güç göstergesi, şifre üretici, yaygın şifre engeli) ve zorunlu güvenlik sorusu. Varsayılan `admin123` hesabı **oluşturulmaz**. |
| 5. Site ve Daireler | Site adı/adresi, kasa devir bakiyesi ve tarihi, bloklar ve blok başına daire sayısı; daireler (ve istenirse kat bilgisi) otomatik oluşturulur. |
| 6. İletişim | Genel adres, destek e-postası, SMTP (test e-postası göndererek denenebilir) ve WhatsApp API. Hepsi isteğe bağlıdır. |
| 7. Görsellik | Vurgu rengi, cam/animasyon/hover efektleri ve baloncuk efektleri. |
| 8. Özet ve Kurulum | Bilgileri gözden geçirme ve onay. Tek veritabanı işlemiyle yönetici, site, bloklar, daireler ve ayarlar yazılır; `.env` güncellenir ve kilit dosyası oluşturulur. |

Davranış notları:

- **Kilit:** Kurulum bitince `storage/app/installed.lock` oluşur; bundan sonra `/install` adresleri `404` döner. Kilit dosyası yoksa ancak veritabanında kullanıcı varsa (mevcut kurulumlar, `migrate --seed` ile kurulanlar) sistem kurulu kabul edilir ve kilit otomatik yazılır; yani **mevcut kurulumlar sihirbazdan etkilenmez.**
- **Yeniden kurulum:** Ayarlar → Veritabanı → "Tüm Kayıtları Sil" işlemi kilidi de kaldırır ve sihirbazı yeniden açar.
- **Kapatma:** `.env` içinde `INSTALLER_ENABLED=false` sihirbazı tamamen devre dışı bırakır (yalnızca komut satırıyla kurulum yapacaksanız: `php artisan migrate --seed`).
- **Güvenlik:** Kurulum bitene kadar sihirbaz, adresi bilen herkese açıktır. Yeni bir sunucuyu internete açtığınızda kurulumu hemen tamamlayın.
- **Sunucu yeniden başlatma:** `php artisan serve` kullanıyorsanız ve veritabanı adımından sonra bağlantı yapılandırması yüklenmediyse son adım bunu bildirir; sunucuyu yeniden başlatıp sayfayı yenileyin.

## 📌 Yapılandırma (.env)

| Anahtar | Açıklama |
|---|---|
| `APP_URL` | Uygulamanın adresi. |
| `APP_LOCALE`, `APP_FALLBACK_LOCALE` | Varsayılan `tr`. |
| `INSTALLER_ENABLED` | `true` (varsayılan) iken kurulmamış sistemde Kurulum Sihirbazı açılır; `false` sihirbazı kapatır. |
| `DB_CONNECTION` | `sqlite`, `mysql`, `mariadb`, `pgsql`. MySQL için `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` satırlarını açıp doldurun. |
| `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | Varsayılan `database`. |
| `MAIL_*` | Duyuru e-postaları için SMTP (varsayılan `log`: e-postalar yalnızca log dosyasına yazılır). |

WhatsApp API ve Vakıfbank bilgileri `.env` yerine **panelden** girilir, veritabanında saklanır (token alanları şifrelenir).

## 📌 İlk Giriş ve Güvenlik

Kurulum sihirbazı, girdiğiniz bilgilerle yönetici hesabını oluşturur; varsayılan şifreli hesap yoktur.

Komut satırıyla kurulum yapıyorsanız (`INSTALLER_ENABLED=false` veya sihirbazı atlamak için) `php artisan migrate --seed` şunları oluşturur:

- Kullanıcı: `admin@admin.local` · Şifre: `admin123`
- Örnek site: "Örnek Sitesi"

**Bu yolu kullandıysanız ilk iş olarak şifreyi değiştirin** (Yetkililer → hesap → düzenle) ve bir güvenlik sorusu tanımlayın.

Güvenlik önlemleri:
- Başarısız girişler sayılır; **5 hatalı denemeden sonra hesap 15 dakika kilitlenir.**
- Şifre sıfırlama, güvenlik sorusunun doğru cevaplanmasına ve oturuma bağlı bir doğrulama jetonuna bağlıdır; başarılı sıfırlama kilidi de kaldırır.
- Tüm panel rotaları `auth` arkasındadır; CSRF koruması etkindir.

## 📌 Modüller

- **Aidatlar:** Dönem (yıl/ay) ve daire bazlı tahakkuk; ödeme geldikçe durum (bekliyor/kısmi/ödendi) hesaplanır.
- **Ödemeler:** Yöntemler `bank`, `eft`, `card`, `cash`. Makbuz numarası boş bırakılırsa otomatik üretilir ve benzersizdir. Makbuz sayfası yazdırılabilir; PDF çıktısı ve WhatsApp paylaşımı vardır.
- **Sakinler:** `owner` (ev sahibi) / `tenant` (kiracı) türleri; iş yeri ve iki araç (model + plaka).
- **Duyurular:** Yayın tarihi gelen duyurular üst çubukta kayar. Gönderim ekranından seçili sakinlere WhatsApp (wa.me bağlantısı) veya e-posta ile iletilebilir.
- **Raporlar:** Yıl ve ay filtreli site raporu; A4 yatay PDF. "Aylık Detay" tablosu ve PDF çizelgesi aynı sütunlara ve aynı verilere sahiptir: Ay, Devir, Tahakkuk, Tahsilat, Gelir, Gelir Toplamı, Gider, Kalan Borç, Kasa, Oran. **Gelir Toplamı = Devir + Tahsilat + Gelir** (aylık). Sayfadaki "Oran" rakamları temanın ana yazı rengini (`--text-primary`) kullanır; aydınlık ve karanlık modda okunur kalır.
- **Vakıfbank:** Banka hareketleri başarılı/başarısız/bekleyen olarak listelenir, başarısızlar elle onaylanıp ödemeye dönüştürülebilir. Canlı SOAP çağrısı henüz etkin değildir; mevcut servis (`VakifbankSyncService`) kontrol altyapısını ve zamanlamayı sağlar.

## 📌 Ayarlar Sayfası

`Ayarlar` menüsünde dört sekme vardır:

1. **Genel** – Genel erişim adresi, destek e-postası.
2. **WhatsApp API** – Endpoint, oturum/cihaz kimliği, token, grup kimliği. Etkinleştirirken zorunlu alanlar hem tarayıcıda hem sunucuda doğrulanır; boş token alanı mevcut token'ı korur.
3. **Görsellik** – Tek formda, tek **Kaydet** düğmesiyle:
   - *Arayüz Efektleri:* vurgu rengi (renk ve adıyla), cam (bulanıklık), geçiş/çubuk animasyonları, kart hover animasyonları.
   - *Baloncuk Efektleri:* arka plan baloncukları; sayı, hız, opaklık, renk modu, boyut.
4. **Veritabanı** – Yedekleme / geri yükleme / sıfırlama (yalnızca admin). Sürücü, tablo sayısı ve toplam kayıt bilgisi üstte gösterilir.

Ayarlar `system_settings` tablosunda (`group`, `key`, `value`, `is_encrypted`) tutulur.

## 📌 Veritabanı Yedekleme ve Geri Yükleme

`Ayarlar → Veritabanı` (yalnızca `admin`):

| İşlem | Açıklama |
|---|---|
| **JSON Yedeği İndir** | Veri tablolarını `site360pro-database-backup-v1` biçiminde indirir. Geri yükleme bu dosyayla yapılır. |
| **SQL Yedeği İndir** | `INSERT` ifadelerinden oluşan döküm (harici araçlarla içe aktarmak için); panelden geri yüklenemez. |
| **Yedekten Geri Yükle** | JSON yedeğini yükler; onay alanına `GERI_YUKLE` yazılır. Yabancı anahtar kontrolleri işlem boyunca kapatılır, tablolar yedekteki kayıtlarla değiştirilir. |
| **Tüm Kayıtları Sil** | Şema ve migration geçmişi korunur, tüm kayıtlar (kullanıcılar dahil) silinir; onay alanına `VERITABANINI_SIL` yazılır. **Geri alınamaz.** İşlemden sonra kurulum kilidi kalkar ve Kurulum Sihirbazı yeniden açılır. |

`migrations`, `sessions`, `cache`, `cache_locks` tabloları yedeğe dahil edilmez. Mantık `app/Services/DatabaseBackupService.php` içindedir.

## 📌 Daire Sahibi Raporları

- Daire bazlı rapor: `/owner-reports/apartment/{daire}` (PDF: `/apartments/{daire}/report/pdf`). Daireler, sakinler ve aidatlar listelerindeki rapor düğmeleri bu sayfaya gider.
- Token bağlantısı: `/rapor/{token}` (PDF: `/reports/token/{token}/pdf`). Bağlantılar `owner_report_links` tablosunda saklanır, bir son kullanma tarihi vardır; süresi dolan bağlantı `410` döner, her açılışta `last_used_at` güncellenir. Bağlantı oluşturma/silme uçları: `POST /owner-report-links`, `DELETE /owner-report-links/{id}` (oturum gerekir).

## 📌 Rol ve Yetkiler

- **admin** – Tüm sitelere ve ayarlara erişir; veritabanı işlemleri yalnızca bu role açıktır.
- **site_manager** – Kendisine atanan site ile sınırlıdır (`site_id`).

Herkese açık olanlar: giriş sayfası, şifre sıfırlama uçları, daire/token rapor sayfaları ve PDF'leri, tema değiştirme.

## 📌 Zamanlanmış Görevler ve Kuyruk

```bash
php artisan bank:sync-vakifbank              # aktif tüm entegrasyonlar
php artisan bank:sync-vakifbank --site_id=1  # tek site
```

Komut her dakika zamanlanmıştır. Zamanlayıcıyı çalıştırmak için cron/Görev Zamanlayıcı'ya ekleyin:

```cron
* * * * * cd /proje/yolu && php artisan schedule:run >> /dev/null 2>&1
```

`QUEUE_CONNECTION=database` kullanıyorsanız `php artisan queue:work` komutunu bir süreç yöneticisiyle çalıştırın.

## 📌 Veritabanı Şeması

| Tablo | Amaç |
|---|---|
| `users` | Panel kullanıcıları (rol, site, güvenlik sorusu, hatalı giriş/kilit alanları) |
| `sites`, `building_blocks`, `apartments` | Site → blok → daire hiyerarşisi |
| `residents` | Sakinler (tür, iş yeri, araçlar) |
| `dues`, `payments` | Tahakkuklar ve ödemeler (makbuz no, yöntem) |
| `incomes`, `expenses` | Gelir ve giderler |
| `announcements` | Duyurular |
| `owner_report_links` | Daire sahibi rapor bağlantıları |
| `bank_integrations`, `bank_transactions` | Vakıfbank entegrasyonu |
| `system_settings` | Panel ayarları (şifreli değer desteği) |
| `audit_logs` | İşlem günlüğü |
| `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens` | Laravel altyapı tabloları |

## 📌 Proje Yapısı

```
app/
  Http/Controllers/     Site, Apartment, Resident, Due, Payment, Income, Expense,
                        Announcement, Authority, Report, OwnerReport, Settings,
                        DatabaseSettings, Integration, Auth, PasswordSecurityReset, Install,
                        Dashboard, Theme
  Models/               Eloquent modelleri (SystemSetting: ayarlar + Görsellik öznitelikleri)
  Services/             DatabaseBackupService, VakifbankSyncService
  Services/Installer/   EnvironmentWriter, RequirementsChecker, InstallationState,
                        DatabaseInstaller, InstallationFinalizer
  Http/Middleware/      InstallationGuard (kurulmamış sistemi sihirbaza yönlendirir)
  Providers/            AppServiceProvider (Bootstrap 5 sayfalama, SQLite MONTH/YEAR/DAY, kurulum servisleri)
bootstrap/              Uygulama başlatma
config/                 Laravel yapılandırmaları
database/
  migrations/           Şema ve ayar tohumlama migration'ları
  seeders/              Varsayılan admin ve örnek site
lang/tr/                Türkçe doğrulama mesajları
public/                 Web kökü (derleme çıktısı: public/build)
resources/
  css/app.css           TÜM stiller
  js/app.js             TÜM istemci kodu
  views/                Blade şablonları (layouts, auth, settings, ... *pdf.blade.php)
routes/
  web.php               HTTP rotaları
  console.php           Artisan komutları + zamanlama
tests/                  PHPUnit testleri
```

## 📌 Ön Yüz (CSS / JS) Mimarisi

Blade dosyalarında `<style>` / `<script>` bloğu **yoktur**; tüm stil ve betikler `resources/css/app.css` ve `resources/js/app.js` içindedir ve her layout `@vite([...])` ile bunları yükler. Sunucudan gelen değerler özniteliklerle aktarılır:

| Amaç | Mekanizma |
|---|---|
| Görsellik ayarları | `<html data-fx data-fx-glass data-fx-anim data-fx-hover data-fx-bubble style="--fx-accent…">` (`SystemSetting::htmlAttributes()`); kurallar `app.css` sonundadır. |
| Baloncuklar | `<canvas id="bubbleEffectCanvas" data-bubble data-accent>`; `initBubbleEffect()`. |
| Flash mesajları | `#appToastContainer[data-flash]` → `window.showToast()`. |
| Şifremi Unuttum | `#forgotPasswordModal[data-question-url, data-verify-url, data-reset-url]`; tetikleyiciler `#btnOpenForgotPassword`, `[data-forgot-email]`. |
| Onay penceresi | `form[data-confirm="mesaj"]` |
| Filtre select'i | `select[data-auto-submit]` değişince formu gönderir. |
| Kopyalama, tümünü seç, WhatsApp araçları | `[data-copy-target]`, `[data-toggle-checks]`, `#whatsappMessage` vb. |

**İstisna:** `*pdf.blade.php` şablonlarında (dompdf) stiller bilinçli olarak satır içi bırakılmıştır; dompdf Vite çıktısını yükleyemez.

**Yerleşim boşlukları:**

| Ne | Nerede (`resources/css/app.css`) |
|---|---|
| Sayfa içeriğinin sol/sağ kenar boşluğu (masaüstü) | `.app-main .container-xl` → `padding-left` / `padding-right` (varsayılan `0.75rem`) |
| Aynı boşluk, mobil | `@media (max-width: 991.98px)` içindeki `.app-main .container-xl` |
| Üst çubuk (header) yüksekliği ve iç boşluğu | `.app-topbar` → `min-height`, `padding` (üst, sağ, alt, sol) |
| Karanlık mod butonu boyutu | `.theme-toggle-btn` → `width` / `height` |
| Kullanıcı menüsü boşluğu | `.user-menu` → `padding` |

**Giriş ekranı:** Sayfa `body.login-page` sınıfını taşır. Masaüstünde ekranı tam kaplar ve kaydırma çubuğu çıkmaz (`html` kaydırması kapatılır); mobilde bölümler alt alta dizilir ve normal kaydırılır. "Şifremi Unuttum" modalı açılırken Bootstrap'ın `body` üzerine eklediği sağ boşluk (`padding-right`) bu sayfada sıfırlandığı için düzen kaymaz. Sol üstteki logo ve "Site360Pro" yazısı bağlantı değildir.

**WhatsApp destek butonu:** Sağ altta sabit durur; konumu `resources/views/layouts/app.blade.php` içinde butonun satır içi `style` özniteliğindedir (`right: 20px; bottom: 40px;`). Animasyonu `.wa-circle-animated` sınıfından gelir.

**Kod düzeni:** `app/`, `config/`, `database/` (migration ve seeder), `lang/`, `routes/`, `tests/`, `resources/` ve `public/index.php` içindeki kodlarda açıklama satırı bulunmaz. PHP dosyaları Laravel Pint, CSS ve JS dosyaları Prettier ile biçimlendirilmiştir (`vendor/bin/pint`, `npx prettier --write resources/css/app.css resources/js/app.js`).

## 📌 Test

```bash
php artisan test
```

## 📌 Üretime Alma

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

- `.env`: `APP_ENV=production`, `APP_DEBUG=false`.
- Web sunucusu belge kökü `public/`; `storage/` ve `bootstrap/cache/` yazılabilir olmalı.
- Varsayılan admin şifresini değiştirin, düzenli yedek alın (Ayarlar → Veritabanı).

## 📌 Sorun Giderme

| Belirti | Çözüm |
|---|---|
| Toast, vurgu rengi, baloncuk veya yeni stiller çalışmıyor; üstte boşluk var | `npm run build` çalıştırın, tarayıcıda Ctrl+F5 yapın. |
| `Vite manifest not found` | `npm run build` (veya `npm run dev`). |
| Sihirbaz açılıyor ama düzensiz görünüyor | `npm run build` çalıştırın; `public/build` güncel değil. |
| Sihirbaz ".env dosyasına yazılamadı" diyor | `.env` dosyasına ve proje klasörüne web sunucusu kullanıcısı için yazma izni verin. |
| Kurulmuş sistemde sihirbaz yeniden açılmasın | Kilit dosyası `storage/app/installed.lock` silinmemeli; silinse bile kullanıcı kaydı varsa otomatik yeniden yazılır. |
| Composer "PHP >= 8.4" uyarısı | Kurulu bağımlılıklar daha yeni PHP istiyor; PHP sürümünü yükseltin. |
| Geri yükleme "geçerli değil" diyor | Yalnızca panelden alınan **JSON** yedeği yüklenebilir. |
| Hesap kilitlendi | 15 dakika bekleyin veya Şifremi Unuttum akışıyla şifreyi sıfırlayın (kilit kalkar). |

## 📌 İlk Kurulum Kasa Devri

Site ekleme/düzenleme formunda **İlk Kurulum Kasa Devri (₺)** ve **Devir Tarihi** (zorunlu) alanları bulunur.

- Kasa hesabı, devir tarihinin ait olduğu ayın başından itibaren yapılır; öncesindeki kayıtlar kasaya dahil edilmez.
- Kasa = Devir + Tahsilat + Gelir − Gider (devir ayından itibaren kümülatif).
- Ana panel "Kasa Bakiyesi" ve Raporlar "Dönem Geliri / Dönem Sonu Kasa" bu değeri baz alır.
- Devir tarihinden önceki aylarda kasa 0 gösterilir.
- Kurulum: `php artisan migrate` (sites tablosuna `opening_balance`, `opening_balance_date` eklenir). Mevcut sitelerde alanlar boştur; sitenin bir sonraki düzenlemesinde doldurulması gerekir, doldurulana kadar eski davranış (tüm kayıtlar) sürer.

## 📄 Lisans

Bu proje [Unlicense](https://unlicense.org/) ile kamu malı olarak sunulmuştur; dilediğiniz gibi kullanabilir, değiştirebilir ve dağıtabilirsiniz. Ayrıntılar için [`LICENSE`](LICENSE) dosyasına bakın.

## 🕰️ Son Güncelleme
28 Ekim 2026

---

<p align="center">❤️ Made with Love ❤️</p>

---
