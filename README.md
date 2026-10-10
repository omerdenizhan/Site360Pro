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
- **Veritabanı:** SQLite (varsayılan), MySQL/MariaDB veya PostgreSQL
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

# 2) PHP bağımlılıkları
composer install

# 3) Ortam dosyası ve uygulama anahtarı
cp .env.example .env
php artisan key:generate

# 4) SQLite için veritabanı dosyası
touch database/database.sqlite          # Windows: type nul > database\database.sqlite

# 5) Tablolar ve başlangıç verisi
php artisan migrate --seed

# 6) Ön yüz derlemesi
npm install
npm run build                            # geliştirme: npm run dev

# 7) Çalıştırma
php artisan serve
```

Tarayıcıdan `http://localhost:8000` adresini açın. Laragon/XAMPP gibi ortamlarda proje klasörünü `www` altına koyup sanal host kullanabilirsiniz; **belge kökü `public/` klasörü olmalıdır.**

> **Önemli:** `resources/css/app.css` veya `resources/js/app.js` değiştiğinde `npm run build` çalıştırın (ya da geliştirme sırasında `npm run dev` açık tutun). Tarayıcı, `public/build` içindeki derlenmiş dosyaları yükler; derleme yapılmazsa stil/JS değişiklikleri görünmez. `php artisan cache:clear` / `optimize:clear` Vite derlemesini etkilemez.

## 📌 Yapılandırma (.env)

| Anahtar | Açıklama |
|---|---|
| `APP_URL` | Uygulamanın adresi. |
| `APP_LOCALE`, `APP_FALLBACK_LOCALE` | Varsayılan `tr`. |
| `DB_CONNECTION` | `sqlite`, `mysql`, `mariadb`, `pgsql`. MySQL için `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` satırlarını açıp doldurun. |
| `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | Varsayılan `database`. |
| `MAIL_*` | Duyuru e-postaları için SMTP (varsayılan `log`: e-postalar yalnızca log dosyasına yazılır). |

WhatsApp API ve Vakıfbank bilgileri `.env` yerine **panelden** girilir, veritabanında saklanır (token alanları şifrelenir).

## 📌 İlk Giriş ve Güvenlik

`php artisan migrate --seed` aşağıdakileri oluşturur:

- Kullanıcı: `admin@admin.local` · Şifre: `admin123`
- Örnek site: "Örnek Sitesi"

**Canlı ortamda ilk iş olarak şifreyi değiştirin** (Yetkililer → hesap → düzenle) ve bir güvenlik sorusu tanımlayın.

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
| **Tüm Kayıtları Sil** | Şema ve migration geçmişi korunur, tüm kayıtlar (kullanıcılar dahil) silinir; onay alanına `VERITABANINI_SIL` yazılır. **Geri alınamaz.** |

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
                        DatabaseSettings, Integration, Auth, PasswordSecurityReset,
                        Dashboard, Theme
  Models/               Eloquent modelleri (SystemSetting: ayarlar + Görsellik öznitelikleri)
  Services/             DatabaseBackupService, VakifbankSyncService
  Providers/            AppServiceProvider (Bootstrap 5 sayfalama, SQLite MONTH/YEAR/DAY)
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

**Sidebar genişliği:** Masaüstünde sidebar kenarındaki tutamaçla sürüklenerek ayarlanır ve tarayıcıda (`localStorage`, `site360pro.sidebarWidth`) saklanır. Sınırlar **180 px – 250 px**'dir ve iki yerde uygulanır: `resources/js/app.js` içindeki `MIN` / `MAX` sabitleri ve `resources/css/app.css` içindeki `--sidebar-w: clamp(180px, var(--appSidebar-sidebar-width), 250px)` (masaüstü bloğu). Varsayılan genişlik `--appSidebar-sidebar-width: 13rem` değeridir; tutamaca çift tıklamak varsayılana döndürür. Sınırı değiştirirken bu iki yeri birlikte güncelleyin ve `npm run build` çalıştırın.

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
| Composer "PHP >= 8.4" uyarısı | Kurulu bağımlılıklar daha yeni PHP istiyor; PHP sürümünü yükseltin. |
| Geri yükleme "geçerli değil" diyor | Yalnızca panelden alınan **JSON** yedeği yüklenebilir. |
| Hesap kilitlendi | 15 dakika bekleyin veya Şifremi Unuttum akışıyla şifreyi sıfırlayın (kilit kalkar). |

## 📄 Lisans

Bu proje [Unlicense](https://unlicense.org/) ile kamu malı olarak sunulmuştur; dilediğiniz gibi kullanabilir, değiştirebilir ve dağıtabilirsiniz. Ayrıntılar için [`LICENSE`](LICENSE) dosyasına bakın.

## 🕰️ Son Güncelleme
10 Ekim 2026

---

<p align="center">❤️ Made with Love ❤️</p>

---

## İlk Kurulum Kasa Devri

Site ekleme/düzenleme formunda **İlk Kurulum Kasa Devri (₺)** ve **Devir Tarihi** (zorunlu) alanları bulunur.

- Kasa hesabı, devir tarihinin ait olduğu ayın başından itibaren yapılır; öncesindeki kayıtlar kasaya dahil edilmez.
- Kasa = Devir + Tahsilat + Gelir − Gider (devir ayından itibaren kümülatif).
- Ana panel "Kasa Bakiyesi" ve Raporlar "Dönem Geliri / Dönem Sonu Kasa" bu değeri baz alır.
- Devir tarihinden önceki aylarda kasa 0 gösterilir.
- Kurulum: `php artisan migrate` (sites tablosuna `opening_balance`, `opening_balance_date` eklenir). Mevcut sitelerde alanlar boştur; sitenin bir sonraki düzenlemesinde doldurulması gerekir, doldurulana kadar eski davranış (tüm kayıtlar) sürer.
