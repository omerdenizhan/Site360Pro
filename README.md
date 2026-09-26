# Site360Pro — Site ve Apartman Yönetim Sistemi

> **Site360Pro**, birden fazla site/blok yapısının, bağımsız bölümlerin, sakinlerin, aidat/tahakkuk kayıtlarının, tahsilatların, gelir-gider hareketlerinin, raporların, yetkili kullanıcıların ve banka hareketlerinin tek bir Laravel uygulaması üzerinden yönetilmesi için geliştirilmiş web tabanlı site yönetim otomasyonudur.

Bu README, **Site360Pro(3).zip** proje arşivinin gerçek dosya yapısı, Composer/NPM bağımlılıkları, route'ları, migration'ları, controller/service sınıfları ve mevcut uygulama davranışı incelenerek hazırlanmıştır.

---

## İçindekiler

- [Proje Özeti](#proje-özeti)
- [Mevcut Sürüm ve Teknik Durum](#mevcut-sürüm-ve-teknik-durum)
- [Özellikler](#özellikler)
- [Teknoloji Yığını](#teknoloji-yığını)
- [Sistem Gereksinimleri](#sistem-gereksinimleri)
- [Kurulum](#kurulum)
- [Ortam Değişkenleri](#ortam-değişkenleri)
- [İlk Giriş](#ilk-giriş)
- [Kullanıcı Rolleri ve Yetkilendirme](#kullanıcı-rolleri-ve-yetkilendirme)
- [Modüller](#modüller)
- [Finansal İşleyiş](#finansal-işleyiş)
- [Raporlama ve PDF](#raporlama-ve-pdf)
- [VakıfBank Entegrasyonu](#vakıfbank-entegrasyonu)
- [Duyuru ve E-posta](#duyuru-ve-e-posta)
- [Telegram Ayarları](#telegram-ayarları)
- [Veritabanı Yedekleme ve Geri Yükleme](#veritabanı-yedekleme-ve-geri-yükleme)
- [Korunan Paket İndirme](#korunan-paket-indirme)
- [Tema](#tema)
- [Zamanlanmış Görevler ve Queue](#zamanlanmış-görevler-ve-queue)
- [Frontend Geliştirme](#frontend-geliştirme)
- [Testler](#testler)
- [Üretim Ortamına Alma](#üretim-ortamına-alma)
- [Güvenlik Notları](#güvenlik-notları)
- [Dizin Yapısı](#dizin-yapısı)
- [Önemli Route'lar](#önemli-route'lar)
- [Veritabanı Yapısı](#veritabanı-yapısı)
- [Bilinen Durumlar ve Geliştirme Notları](#bilinen-durumlar-ve-geliştirme-notları)
- [Sorun Giderme](#sorun-giderme)
- [Lisans](#lisans)

---

## Proje Özeti

Site360Pro aşağıdaki ana iş akışlarını tek bir panelde birleştirir:

1. Site ve blokların tanımlanması
2. Dairelerin/bağımsız bölümlerin oluşturulması
3. Daire sakinlerinin ve malik/kiracı bilgilerinin tutulması
4. Dönemsel aidat/tahakkuk oluşturulması
5. Tahsilatların kaydedilmesi
6. Makbuz ve PDF çıktılarının oluşturulması
7. Gelir ve giderlerin izlenmesi
8. Site bazında finansal raporların oluşturulması
9. Daire sahibi için token tabanlı dış rapor bağlantıları
10. Yetkili kullanıcıların ve site kapsamlarının yönetilmesi
11. İşlem geçmişinin Audit Log ile tutulması
12. VakıfBank entegrasyon ayarları ve banka hareketlerinin eşleştirilmesi için altyapı
13. Duyuru oluşturma ve seçilen sakinlere e-posta gönderme
14. Veritabanı JSON/SQL yedekleme ve JSON geri yükleme
15. Açık/koyu tema seçimi
16. Parola korumalı ZIP paket indirme ekranı

---

# Mevcut Sürüm ve Teknik Durum

Projede bulunan `composer.json` ve `composer.lock` dosyalarına göre:

| Bileşen | Proje gereksinimi | Kilitli sürüm |
|---|---|---|
| PHP | `^8.3` | PHP 8.3+ |
| Laravel Framework | `^13.8` | `v13.33.0` |
| DomPDF | `^3.1` | `v3.1.6` |
| Laravel Tinker | `^3.0` | Composer lock'a göre kurulur |
| Vite | `^8.0` | `8.1.4` |
| Tailwind CSS | `^4.0.0` | `4.3.2` |
| Tabler Core | `^1.4.0` | `1.4.0` |
| Tabler Icons Webfont | `^3.44.0` | package-lock'a göre kurulur |
| Node.js | Vite 8 nedeniyle | `20.19+` veya `22.12+` |
| NPM | Node.js ile birlikte | Güncel sürüm önerilir |

### Önemli sürüm notu

Eski dokümantasyonda Laravel 11 / PHP 8.2 gibi bilgiler bulunabilir. **Bu arşiv için esas alınması gereken sürümler `composer.json`, `composer.lock`, `package.json` ve `package-lock.json` dosyalarındaki sürümlerdir.**

---

# Özellikler

## 1. Site Yönetimi

Site modülü ile:

- Site oluşturma
- Site düzenleme
- Site silme
- Site adresi saklama
- Site bazında blokları ve finansal kayıtları ayırma

işlemleri yapılabilir.

Model:

```text
Site
```

---

## 2. Blok Yönetimi

Her site altında birden fazla blok tutulabilir.

Veri ilişkisi:

```text
Site
 └── BuildingBlock
      └── Apartment
```

Bloklar doğrudan siteye bağlıdır.

---

## 3. Daire Yönetimi

Daire modülü aşağıdaki bilgileri destekler:

- Blok
- Daire/kapı numarası
- Kat numarası
- Doluluk durumu
- Aktif sakin ilişkisi
- Aidat/tahakkuk ilişkisi
- Ödeme ilişkisi
- Daire sahibi raporu

Daire modeli:

```text
Apartment
```

---

## 4. Sakin Yönetimi

Sakin kayıtlarında:

- Ad soyad
- Telefon
- E-posta
- Daire
- Sakin tipi
- Aktif/pasif durumu

tutulur.

Sakin tipi uygulamadaki veri modeline göre kiracı/ev sahibi gibi değerleri temsil etmek için kullanılır.

Model:

```text
Resident
```

---

## 5. Aidat / Tahakkuk Yönetimi

`Due` modeli üzerinden dönemsel borçlar tutulur.

Desteklenen temel bilgiler:

- Daire
- Yıl
- Ay
- Tutar
- Son ödeme tarihi
- Not

Bir tahakkukun bir veya daha fazla ödeme kaydı olabilir.

Örneğin:

```text
Ocak 2026 aidatı
    Tahakkuk: 1.500 TL
    Ödeme  : 1.000 TL
    Kalan  :   500 TL
```

Bu yapı kısmi ödemeleri destekler.

---

## 6. Tahsilat / Ödeme Yönetimi

`Payment` modeli ile tahakkuklara yapılan ödemeler kaydedilir.

Ödeme bilgilerinde:

- Tahakkuk
- Tutar
- Ödeme yöntemi
- Makbuz numarası
- Ödeme tarihi

bulunur.

Ödeme sonrası:

- Makbuz görüntüleme
- PDF makbuz oluşturma
- Daire finansal raporlarına yansıtma

işlemleri yapılabilir.

Banka entegrasyonundan gelen ödemeler ayrıca banka hareketiyle ilişkilendirilebilir.

---

## 7. Gelir Yönetimi

Aidat tahsilatlarından bağımsız diğer gelirler `Income` modeliyle tutulur.

Örnek kategoriler:

- Faiz geliri
- Ortak alan geliri
- Reklam geliri
- Diğer gelirler

Temel alanlar:

- Site
- Kategori
- Açıklama
- Tutar
- Gelir tarihi

---

## 8. Gider Yönetimi

`Expense` modeli üzerinden site giderleri takip edilir.

Temel alanlar:

- Site
- Kategori
- Açıklama
- Tutar
- Gider tarihi

Bu kayıtlar finansal raporlarda ve dashboard özetlerinde kullanılır.

---

## 9. Dashboard

Ana panel seçilen:

- Site
- Blok
- Yıl
- Ay

filtrelerine göre özet bilgi sunar.

Dashboard üzerinde aşağıdaki bilgiler hesaplanır:

- Toplam daire
- Dolu daire
- Boş daire
- Dönem tahakkuku
- Tahsil edilen
- Kalan borç
- Tahsilat oranı
- Kasa bakiyesi
- Günlük tahsilat
- Son ödemeler
- Son gelirler
- Son giderler
- Duyurular
- Son denetim kayıtları
- Öncelikli takip edilmesi gereken borçlar

---

## 10. Finansal Raporlama

`ReportController` site bazında yıllık ve aylık finansal rapor üretir.

Raporlarda:

- Tahakkuk toplamı
- Tahsilat toplamı
- Diğer gelirler
- Giderler
- Kalan borç
- Bakiye
- Tahsilat oranı
- İşlem adetleri
- Tahsilat kaynağı

gibi değerler hesaplanır.

Tahsilat kaynağı üç grupta raporlanabilir:

```text
Otomatik entegrasyon
Manuel entegrasyon
Panelden işlendi
```

---

## 11. Daire Sahibi Raporları

Daire bazında yıllık finansal durum gösterilebilir.

Rapor:

- Aylık aidat
- Ödenen tutar
- Kalan tutar
- Ödeme durumu
- Tahsilat oranı
- Ödeme kaynağı

bilgilerini içerir.

Daire raporu:

- Panel içerisinden
- Token üzerinden herkese açık özel bağlantı ile
- PDF olarak

sunulabilir.

### Token bağlantıları

`OwnerReportLink` modeli ile süreli bağlantı üretilebilir.

Bağlantının:

- Token değeri
- Daire ilişkisi
- Oluşturan kullanıcı
- Son kullanım zamanı
- Son geçerlilik zamanı

saklanır.

Bağlantı süresi dolduğunda uygulama HTTP `410` yanıtı verir.

---

## 12. Kullanıcı ve Yetki Yönetimi

Uygulamada iki ana rol tanımlıdır:

```text
admin
site_manager
```

### Admin

Genel yönetici:

- Kullanıcı/yetkili yönetimi
- Site yönetimi
- Finansal işlemler
- Ayarlar
- Veritabanı yedekleme
- Veritabanı geri yükleme
- Veritabanı temizleme
- Entegrasyon ayarları
- Genel yönetim

işlemlerine erişebilir.

### Site Manager

Site yöneticisi hesabı belirli bir siteye bağlanabilir.

`users.site_id` alanı üzerinden site kapsamı tutulur.

> Not: Projedeki route grubu genel olarak `auth` middleware'i ile korunmaktadır. İşlem bazındaki yetki kısıtları controller seviyesinde uygulanmaktadır. Üretim ortamında daha ayrıntılı Policy/Gate katmanı eklemek önerilir.

---

## 13. Güvenli Giriş

Login sistemi aşağıdaki kontrolleri içerir:

- E-posta doğrulaması
- Aktif kullanıcı kontrolü
- Parola hash kontrolü
- Başarısız giriş sayacı
- 5 başarısız denemeden sonra 15 dakikalık geçici kilit
- Başarılı girişte sayaç sıfırlama
- Session regeneration
- Login/logout Audit Log kaydı

---

## 14. Audit Log

`AuditLog` modeli kritik işlemleri kayıt altına almak için kullanılır.

Örnek olaylar:

- Login
- Logout
- Kullanıcı oluşturma
- Kullanıcı güncelleme
- Kullanıcı silme
- Duyuru oluşturma
- Duyuru gönderme
- Ödeme işlemleri
- Banka eşleştirmeleri
- Entegrasyon ayarları
- Veritabanı geri yükleme
- Rapor bağlantısı oluşturma/silme

Kayıtlarda gerektiğinde:

- Kullanıcı
- İşlem
- Tablo
- Kayıt ID
- IP adresi
- Açıklama
- Tarih

tutulur.

---

# Teknoloji Yığını

## Backend

- PHP 8.3+
- Laravel 13
- Laravel Eloquent ORM
- Blade
- Laravel Session/Auth
- Laravel Validation
- Laravel Scheduler
- Laravel Queue altyapısı

## Frontend

- Blade Templates
- Tailwind CSS 4
- Vite 8
- Tabler Core
- Tabler Icons Webfont
- JavaScript

## Veritabanı

Kod yapısı itibarıyla:

- SQLite
- MySQL / MariaDB
- PostgreSQL

sürücüleriyle çalışabilecek şekilde hazırlanmıştır.

Projede varsayılan `.env` yapılandırması SQLite kullanmaktadır.

## PDF

```text
dompdf/dompdf
```

kullanılır.

PDF üretimi:

- Daire sahibi raporu
- Genel finansal rapor
- Tahsilat makbuzu

için kullanılmaktadır.

---

# Sistem Gereksinimleri

## Zorunlu

### PHP

```text
PHP >= 8.3
```

Kontrol:

```bash
php -v
```

### Composer

Composer 2.x önerilir.

Kontrol:

```bash
composer --version
```

### Node.js

Vite 8'in kilitli sürümü Node.js için:

```text
20.19.0+
```

veya

```text
22.12.0+
```

gerektirir.

Kontrol:

```bash
node -v
npm -v
```

### Veritabanı

Önerilen geliştirme ortamı:

```text
SQLite
```

Üretim ortamı için:

```text
MySQL / MariaDB
```

veya uygun bir PostgreSQL kurulumu kullanılabilir.

---

# Kurulum

## 1. Projeyi çıkarın

ZIP arşivini sunucuda veya geliştirme bilgisayarında uygun bir klasöre çıkarın.

Örneğin:

```text
Site360Pro/
```

Proje kökünde aşağıdaki dosyaların bulunması gerekir:

```text
artisan
composer.json
composer.lock
package.json
package-lock.json
vite.config.js
```

---

## 2. PHP bağımlılıklarını yükleyin

```bash
composer install
```

Üretim ortamında:

```bash
composer install --no-dev --optimize-autoloader
```

---

## 3. `.env` dosyasını oluşturun

Bu arşivde `.env` dosyası bulunmaktadır ancak **`.env.example` bulunmamaktadır**.

Yeni bir kurulum yapıyorsanız `.env` dosyasını manuel olarak oluşturmanız veya mevcut `.env` dosyasını kurulum ortamınıza göre düzenlemeniz gerekir.

Örnek temel yapı:

```env
APP_NAME=Site360Pro
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

APP_TIMEZONE=Europe/Istanbul
APP_LOCALE=tr
APP_FALLBACK_LOCALE=tr
APP_FAKER_LOCALE=tr_TR
```

---

## 4. Uygulama anahtarını oluşturun

Yeni kurulumda:

```bash
php artisan key:generate
```

Bu işlem `.env` içerisindeki `APP_KEY` değerini oluşturur.

> Mevcut üretim sisteminin `.env` dosyasını değiştirirken `APP_KEY` değerini rastgele değiştirmeyin. Şifreli mevcut verilerin okunmasını etkileyebilir.

---

# Veritabanı Kurulumu

## Seçenek A — SQLite

Geliştirme için en kolay yöntemdir.

`.env`:

```env
DB_CONNECTION=sqlite
```

Veritabanı dosyası:

```text
database/database.sqlite
```

Dosya mevcut değilse:

Linux/macOS:

```bash
touch database/database.sqlite
```

Windows PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File
```

Ardından:

```bash
php artisan migrate
```

veya örnek/admin kullanıcı oluşturmak için:

```bash
php artisan migrate --seed
```

---

## Seçenek B — MySQL / MariaDB

`.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=site360pro
DB_USERNAME=site360pro
DB_PASSWORD=GUCLU_BIR_SIFRE
```

Veritabanını oluşturduktan sonra:

```bash
php artisan migrate --seed
```

---

## Seçenek C — PostgreSQL

Örnek:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=site360pro
DB_USERNAME=site360pro
DB_PASSWORD=GUCLU_BIR_SIFRE
```

Ardından:

```bash
php artisan migrate --seed
```

> Veritabanı yedekleme controller'ı SQLite, MySQL/MariaDB ve PostgreSQL sürücülerini özel olarak ele almaktadır. Uygulamanın SQL yedek formatı ise pratikte veritabanı sürücüsüne göre dikkatle test edilmelidir.

---

# Seed İşlemi ve İlk Giriş

`DatabaseSeeder` şu hesabı oluşturur/günceller:

```text
E-posta : admin@admin.local
Şifre   : admin123
Rol     : admin
```

Örnek site de oluşturulur:

```text
Örnek Sitesi
```

### İlk girişten sonra

Üretim ortamında **bu varsayılan hesabın parolasını mutlaka değiştirin** veya hesabı güvenli bir yönetici hesabıyla değiştirin.

> `DatabaseSeeder` her çalıştırıldığında bu kullanıcı `updateOrCreate` ile tekrar ele alınır. Üretimde seed çalıştırmadan önce seeder davranışını kontrol edin.

---

# Uygulamayı Başlatma

## Geliştirme

PHP sunucusu:

```bash
php artisan serve
```

Varsayılan:

```text
http://127.0.0.1:8000
```

Projede bulunan `.env` örneğinde uygulama URL'si:

```text
http://localhost:8001
```

olarak ayarlanmıştır. `php artisan serve --port=8001` kullanmak isterseniz:

```bash
php artisan serve --port=8001
```

---

## Frontend

Bağımlılıkları yükleyin:

```bash
npm install
```

Geliştirme:

```bash
npm run dev
```

Production build:

```bash
npm run build
```

---

# Tek Komutlu Proje Kurulumu

`composer.json` içerisinde aşağıdaki Composer script'i bulunmaktadır:

```bash
composer run setup
```

Bu script sırasıyla:

```text
composer install
.env oluşturma kontrolü
APP_KEY oluşturma
migration
npm install
npm run build
```

işlemlerini çalıştırır.

Ancak bu arşivde `.env.example` bulunmadığından `.env` oluşturma adımına güvenmek yerine kurulumdan önce `.env` dosyasını kendiniz hazırlamanız önerilir.

---

# Modüller

| Modül | Controller | Model |
|---|---|---|
| Dashboard | `DashboardController` | Birden fazla model |
| Site | `SiteController` | `Site` |
| Daire | `ApartmentController` | `Apartment` |
| Sakin | `ResidentController` | `Resident` |
| Aidat/Tahakkuk | `DueController` | `Due` |
| Ödeme | `PaymentController` | `Payment` |
| Gider | `ExpenseController` | `Expense` |
| Gelir | `IncomeController` | `Income` |
| Duyuru | `AnnouncementController` | `Announcement` |
| Yetkililer | `AuthorityController` | `User` |
| Raporlar | `ReportController` | Finansal modeller |
| Daire Raporu | `OwnerReportController` | `OwnerReportLink` |
| Veritabanı | `DatabaseSettingsController` | - |
| VakıfBank | `IntegrationController` | `BankIntegration`, `BankTransaction` |
| Ayarlar | `SettingsController` | `SystemSetting` |
| Tema | `ThemeController` | Session |
| Paket İndirme | `DownloadController` | - |
| Kimlik Doğrulama | `AuthController` | `User` |

---

# Finansal İşleyiş

Finansal akış temel olarak:

```text
Site
  ↓
Blok
  ↓
Daire
  ↓
Aidat/Tahakkuk
  ↓
Ödeme
```

şeklindedir.

Diğer gelir ve giderler ise:

```text
Site
 ├── Income
 └── Expense
```

şeklinde tutulur.

Dashboard'daki kasa hesabında temel yaklaşım:

```text
Kasa Bakiyesi =
Aidat Tahsilatları
+ Diğer Gelirler
- Giderler
```

şeklindedir.

> Bu değer muhasebe sistemi anlamında banka/kasa hesaplarının tüm muhasebe kurallarıyla işlenmiş resmi bilanço değildir; uygulamadaki mevcut gelir-gider hesaplamasına dayalı operasyonel bakiyedir.

---

# Raporlama ve PDF

## Genel finansal rapor

Adres:

```text
/reports
```

PDF:

```text
/reports/pdf
```

Filtreler:

```text
site_id
year
month
```

Örnek:

```text
/reports?site_id=1&year=2026&month=9
```

PDF:

```text
/reports/pdf?site_id=1&year=2026&month=9
```

---

## Daire raporu

Panel üzerinden:

```text
/apartments/{apartment}/report/pdf
```

Token üzerinden:

```text
/reports/token/{token}/pdf
```

Web görünümü:

```text
/owner-reports/{token}
```

Ayrıca:

```text
/rapor/{token}
```

route'u da aynı token tabanlı rapora yönelir.

---

## DomPDF

PDF üretiminde:

```php
defaultFont = DejaVu Sans
```

kullanılmaktadır.

Bu tercih Türkçe karakterlerin PDF içinde düzgün oluşturulmasına yardımcı olur.

---

# VakıfBank Entegrasyonu

Projede VakıfBank için kapsamlı bir entegrasyon veri modeli ve yönetim ekranı bulunmaktadır.

Ana modeller:

```text
BankIntegration
BankTransaction
```

Servis:

```text
App\Services\VakifbankSyncService
```

---

## Saklanan entegrasyon bilgileri

Site bazında:

- Ortam (`test` / `production`)
- Müşteri numarası
- Hesap numarası
- IBAN
- Kurumsal kullanıcı adı
- Kurumsal parola
- SOAP servis URL'si
- Senkronizasyon aralığı
- Aktif/pasif durumu
- Açıklama şablonu
- Son senkronizasyon zamanı

tutulabilir.

---

## Banka hareketleri

`BankTransaction` aşağıdaki bilgileri saklayabilir:

- Banka işlem ID'si
- Operasyon numarası
- İşlem tarihi
- Tutar
- Yön
- Gönderici adı
- Gönderici IBAN
- Açıklama
- Durum
- Eşleşen aidat
- Eşleşen ödeme
- Eşleşme nedeni
- Hata nedeni
- Ham payload
- İşlenme zamanı

---

## Otomatik eşleştirme mantığı

Entegrasyon ekranında açıklama üzerinden eşleşme değerlendirmesi yapılırken:

- Daire numarası
- Ay/dönem
- Sakin adı
- Tutar

gibi bilgiler dikkate alınır.

Daire kodu bulunamadığında işlem manuel incelemeye bırakılır.

Birden fazla eşleşme olasılığı bulunduğunda otomatik ödeme oluşturulmaz.

Dönem net değilse manuel onay gerekir.

Bu yaklaşım yanlış banka hareketinin otomatik olarak yanlış daireye işlenmesi riskini azaltmak amacıyla kullanılmıştır.

---

## Kritik durum: Canlı SOAP senkronizasyonu

Mevcut `VakifbankSyncService` içinde canlı VakıfBank SOAP servisine gerçek hesap hareketi çağrısı henüz uygulanmış değildir.

Mevcut `sync()` metodu:

1. Entegrasyon bilgilerinin hazır olup olmadığını doğrular.
2. `last_synced_at` değerini günceller.
3. Gerçek banka hareketi çekmek yerine kontrolün çalıştırıldığını bildirir.
4. Yeni/eşleşen/manuel hareket sayılarını mevcut durumda `0` olarak döndürür.

Dolayısıyla **mevcut proje, canlı VakıfBank hesabından hareket çekme özelliğini tamamlanmış bir üretim entegrasyonu olarak değerlendirmemelidir.**

Canlı entegrasyon için `VakifbankSyncService` içerisine gerçek SOAP istemci çağrısı, response mapping, hata yönetimi ve idempotency mekanizması eklenmelidir.

---

# Duyuru ve E-posta

Duyurular:

- Site bazında oluşturulabilir.
- Başlık ve içerik taşıyabilir.
- Yayın tarihi tutulur.
- Site sakinlerine göre filtrelenebilir.
- Blok bazında alıcı seçimi yapılabilir.
- Seçilen sakinlere e-posta gönderilebilir.

Gönderim:

```php
Illuminate\Support\Facades\Mail
```

üzerinden yapılmaktadır.

---

## Mail yapılandırması

Örneğin SMTP için `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=mail@example.com
MAIL_PASSWORD=GUCLU_MAIL_SIFRE
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=mail@example.com
MAIL_FROM_NAME="Site360Pro"
```

Mevcut proje `.env` örneğinde:

```env
MAIL_MAILER=log
```

kullanıldığı için gerçek e-posta göndermek istiyorsanız mail ayarlarını üretim SMTP servisinizle değiştirmeniz gerekir.

---

# Telegram Ayarları

Sistem ayarlarında Telegram için:

- Bot token
- Chat ID
- Aktif/pasif durumu

saklanabilmektedir.

Bot token `SystemSetting` üzerinden şifreli olarak saklanacak şekilde tasarlanmıştır.

### Önemli

Mevcut kodda Telegram ayarlarının kaydedilmesi vardır; ancak bu arşivde Telegram'a bildirim gönderen bağımsız bir servis/iş akışı bulunmamaktadır.

Bu nedenle:

> **Telegram ayar ekranı mevcut olmakla birlikte Telegram mesaj gönderiminin tamamlanmış bir özellik olduğu varsayılmamalıdır.**

---

# Veritabanı Yedekleme ve Geri Yükleme

Veritabanı ayarları:

```text
/settings
```

altındaki veritabanı işlemleri üzerinden yönetilir.

## JSON yedeği

```text
/settings/database/backup/json
```

Tüm tabloların kayıtlarını JSON formatında dışa aktarır.

Yedek formatı:

```text
site360pro-database-backup-v1
```

olarak işaretlenmektedir.

---

## SQL yedeği

```text
/settings/database/backup/sql
```

SQL `INSERT` ifadeleri oluşturarak veri yedeği üretir.

---

## JSON geri yükleme

```text
/settings/database/restore
```

yalnızca doğrulanmış Site360Pro JSON yedeğini kabul eder.

Geri yükleme sırasında:

- Yedek formatı kontrol edilir.
- Transaction kullanılır.
- Mevcut ilgili tablo kayıtları temizlenir.
- Yedek kayıtları yeniden eklenir.
- İşlem Audit Log'a yazılır.

---

## Veritabanını temizleme

```text
/settings/database/wipe
```

migration tablosunu koruyarak diğer tablolardaki kayıtları silmek için kullanılabilir.

Bu işlem geri dönüşü zor olduğundan yalnızca yetkili kullanıcı tarafından ve açık onay metniyle çalıştırılacak şekilde tasarlanmıştır.

---

# Korunan Paket İndirme

Uygulamada:

```text
/downloads
```

adresi üzerinden parola korumalı bir ZIP indirme ekranı vardır.

Parola:

```text
DOWNLOAD_PASSWORD_HASH
```

ile hash olarak saklanabilir.

Arşiv adı:

```text
DOWNLOAD_ARCHIVE_NAME
```

ile ayarlanabilir.

Örnek:

```env
DOWNLOAD_ARCHIVE_NAME=Site360Pro-v1.0.8-full.zip
DOWNLOAD_PASSWORD_HASH='$2y$12$...'
```

İndirme işlemi bcrypt hash ile doğrulanmaktadır.

---

# Tema

Tema değiştirme route'u:

```text
POST /toggle-theme
```

desteklenen değerler:

```text
light
dark
```

Tema session içerisinde tutulur.

Frontend tarafında Tabler tabanlı arayüz kullanılmaktadır.

---

# Zamanlanmış Görevler ve Queue

`routes/console.php` içerisinde:

```bash
php artisan bank:sync-vakifbank
```

komutu tanımlanmıştır.

Belirli bir site için:

```bash
php artisan bank:sync-vakifbank --site_id=1
```

çalıştırılabilir.

Scheduler:

```php
Schedule::command('bank:sync-vakifbank')
    ->everyMinute()
    ->withoutOverlapping();
```

şeklindedir.

## Sunucuda scheduler

Laravel scheduler'ın çalışması için cron üzerinden:

```bash
* * * * * cd /path/to/Site360Pro && php artisan schedule:run >> /dev/null 2>&1
```

benzeri bir görev tanımlanmalıdır.

> Ancak yukarıdaki VakıfBank canlı SOAP entegrasyonu tamamlanmadığı için scheduler'ın mevcut hali gerçek banka hareketlerini çekmez; mevcut entegrasyon kontrol servisinin davranışını çalıştırır.

---

## Queue

`.env` içerisinde:

```env
QUEUE_CONNECTION=database
```

kullanılmaktadır.

Queue tabloları migration'larda mevcuttur.

Queue worker:

```bash
php artisan queue:work
```

veya geliştirme sırasında:

```bash
php artisan queue:listen
```

ile çalıştırılabilir.

---

# Frontend Geliştirme

## Geliştirme

```bash
npm install
npm run dev
```

## Production

```bash
npm run build
```

Vite giriş dosyaları:

```text
resources/css/app.css
resources/js/app.js
```

Vite yapılandırması:

```text
vite.config.js
```

---

# Testler

Projede PHPUnit tabanlı test altyapısı bulunmaktadır.

Testleri çalıştırmak için:

```bash
php artisan test
```

veya:

```bash
composer run test
```

---

## Mevcut test kapsamı

### DownloadTest

- İndirme sayfasının açılması
- Yanlış parola ile indirme yapılamaması
- Doğru parola ile ZIP indirme

test edilir.

### ThemeToggleTest

- Koyu tema
- Açık tema
- Geçersiz tema değerinin varsayılan temaya dönmesi
- Login sayfasındaki tema kontrolü
- Dashboard tema kontrolü

test edilir.

### ExampleTest

- Guest kullanıcının login'e yönlendirilmesi
- Authenticated kullanıcının dashboard'a erişmesi

test edilir.

---

## Testlerde dikkat edilmesi gereken nokta

Mevcut `DatabaseSeeder` örnek site olarak:

```text
Örnek Sitesi
```

oluştururken `ExampleTest` içerisinde:

```text
Abc Sitesi
```

metninin beklendiği görülmektedir.

Bu iki değer birbiriyle uyumlu değildir.

Dolayısıyla temiz bir veritabanında test çalıştırıldığında ilgili testin başarısız olması mümkündür. Test beklentisinin güncel seeder verisiyle eşleştirilmesi önerilir.

---

# Üretim Ortamına Alma

## 1. `.env`

Üretimde:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://alanadiniz.com
```

kullanın.

---

## 2. Güçlü uygulama anahtarı

```bash
php artisan key:generate
```

Yeni kurulumda çalıştırılmalıdır.

Mevcut üretim sisteminde `APP_KEY` rastgele değiştirilmemelidir.

---

## 3. Composer

```bash
composer install --no-dev --optimize-autoloader
```

---

## 4. Frontend

```bash
npm ci
npm run build
```

Production sunucusunda Node.js yalnızca build aşamasında kullanılacaksa build sonrasında Node.js çalıştırmanız gerekmez.

---

## 5. Migration

```bash
php artisan migrate --force
```

---

## 6. Cache

```bash
php artisan optimize
```

Gerekirse:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 7. Storage

Laravel'in storage bağlantısı gerekiyorsa:

```bash
php artisan storage:link
```

---

## 8. Web server

Web sunucusunun document root'u proje köküne değil:

```text
/public
```

klasörüne verilmelidir.

Örneğin:

```text
/var/www/site360pro/public
```

---

# Nginx Örneği

Temel yaklaşım:

```nginx
server {
    listen 80;
    server_name site.example.com;

    root /var/www/site360pro/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Sunucu dağıtımına göre PHP-FPM socket yolu değişebilir.

---

# Güvenlik Notları

## 1. `.env` dosyasını yayınlamayın

Arşivde `.env` dosyası bulunduğu için özellikle dikkat edilmelidir.

Git kullanıyorsanız:

```text
.env
```

dosyasının repository'ye gönderilmediğinden emin olun.

`.gitignore` içerisinde `.env` bulunması gerekir.

---

## 2. Üretimde APP_DEBUG kapalı olmalı

```env
APP_DEBUG=false
```

---

## 3. Varsayılan admin parolasını değiştirin

Seeder'daki:

```text
admin@admin.local
admin123
```

bilgileri yalnızca ilk geliştirme kurulumu içindir.

Üretimde kullanılmamalıdır.

---

## 4. Banka kimlik bilgilerini koruyun

VakıfBank:

- Kurumsal kullanıcı adı
- Kurumsal parola
- Müşteri numarası
- Hesap bilgileri

gibi bilgiler hassastır.

Üretim sisteminde:

- HTTPS
- Güçlü parola
- Minimum yetki
- Güvenli sunucu
- Log erişim kontrolü
- Düzenli yedek

uygulanmalıdır.

---

## 5. Veritabanı yedekleri

JSON ve SQL yedeklerini web root altında herkese açık bir klasörde tutmayın.

Yedekler:

- erişim kontrolü olan
- ayrı depolanan
- mümkünse şifrelenmiş
- düzenli olarak test edilen

bir yapıda tutulmalıdır.

---

## 6. Owner report token'ları

Daire sahibi rapor bağlantıları finansal bilgi içerebildiğinden token'ların gizli tutulması gerekir.

Token URL'sini yalnızca ilgili kişiye gönderin.

Üretimde HTTPS zorunlu tutulmalıdır.

---

# Dizin Yapısı

```text
Site360Pro/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AnnouncementController.php
│   │       ├── ApartmentController.php
│   │       ├── AuthController.php
│   │       ├── AuthorityController.php
│   │       ├── DashboardController.php
│   │       ├── DatabaseSettingsController.php
│   │       ├── DownloadController.php
│   │       ├── DueController.php
│   │       ├── ExpenseController.php
│   │       ├── IncomeController.php
│   │       ├── IntegrationController.php
│   │       ├── OwnerReportController.php
│   │       ├── PaymentController.php
│   │       ├── ReportController.php
│   │       ├── ResidentController.php
│   │       ├── SettingsController.php
│   │       ├── SiteController.php
│   │       └── ThemeController.php
│   │
│   ├── Models/
│   │   ├── Announcement.php
│   │   ├── Apartment.php
│   │   ├── AuditLog.php
│   │   ├── BankIntegration.php
│   │   ├── BankTransaction.php
│   │   ├── BuildingBlock.php
│   │   ├── Due.php
│   │   ├── Expense.php
│   │   ├── Income.php
│   │   ├── OwnerReportLink.php
│   │   ├── Payment.php
│   │   ├── Resident.php
│   │   ├── Site.php
│   │   ├── SystemSetting.php
│   │   └── User.php
│   │
│   ├── Services/
│   │   └── VakifbankSyncService.php
│   │
│   └── Providers/
│
├── bootstrap/
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── downloads.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   └── session.php
│
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php
│
├── public/
│
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── announcements/
│       ├── apartments/
│       ├── auth/
│       ├── authorities/
│       ├── components/
│       ├── dues/
│       ├── expenses/
│       ├── incomes/
│       ├── integrations/
│       ├── layouts/
│       ├── owner-reports/
│       ├── payments/
│       ├── reports/
│       ├── residents/
│       ├── settings/
│       ├── sites/
│       └── dashboard.blade.php
│
├── routes/
│   ├── console.php
│   └── web.php
│
├── tests/
│   ├── Feature/
│   └── Unit/
│
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
└── vite.config.js
```

---

# Önemli Route'lar

## Kimlik doğrulama

```text
GET  /login
POST /login
POST /logout
```

## Dashboard

```text
GET /
```

## Siteler

```text
GET    /sites
POST   /sites
GET    /sites/{site}/edit
PUT    /sites/{site}
DELETE /sites/{site}
```

## Daireler

```text
GET    /apartments
POST   /apartments
GET    /apartments/{apartment}/edit
PUT    /apartments/{apartment}
DELETE /apartments/{apartment}
```

## Sakinler

```text
GET    /residents
POST   /residents
GET    /residents/{resident}/edit
PUT    /residents/{resident}
DELETE /residents/{resident}
```

## Aidatlar

```text
GET    /dues
POST   /dues
GET    /dues/{due}/edit
PUT    /dues/{due}
DELETE /dues/{due}
```

## Ödemeler

```text
GET    /payments
POST   /payments
GET    /payments/{payment}/edit
PUT    /payments/{payment}
DELETE /payments/{payment}

GET /payments/{payment}/receipt
GET /payments/{payment}/receipt/pdf
```

## Gelir/Gider

```text
/incomes
/expenses
```

## Duyurular

```text
/announcements
/announcements/{announcement}/send
POST /announcements/{announcement}/mail
```

## Raporlar

```text
/reports
/reports/pdf
```

## Daire sahibi raporları

```text
/owner-reports/{token}
/reports/token/{token}/pdf
/apartments/{apartment}/report/pdf
/rapor/{token}
```

## VakıfBank

```text
/integrations/vakifbank
POST /integrations/vakifbank/sync
POST /integrations/vakifbank/settings
POST /integrations/vakifbank/transactions/{bankTransaction}/approve
```

## Ayarlar

```text
/settings
POST /settings/telegram
POST /settings/general
```

## Veritabanı

```text
GET  /settings/database/backup/sql
GET  /settings/database/backup/json
POST /settings/database/restore
POST /settings/database/wipe
```

---

# Veritabanı Yapısı

Ana tablolar:

```text
sites
building_blocks
apartments
residents
dues
payments
incomes
expenses
announcements
users
audit_logs
owner_report_links
bank_integrations
bank_transactions
system_settings
```

Laravel altyapı tabloları:

```text
cache
cache_locks
jobs
job_batches
failed_jobs
sessions
password_reset_tokens
migrations
```

Ek olarak projede:

```text
android_metadata
```

migration'ı bulunmaktadır.

---

# Model İlişkileri

Temel ilişki ağacı:

```text
Site
├── BuildingBlock
│   └── Apartment
│       ├── Resident
│       ├── Due
│       │   └── Payment
│       └── OwnerReportLink
│
├── Income
├── Expense
├── Announcement
└── BankIntegration
    └── BankTransaction
```

Kullanıcı:

```text
User
├── Site
└── AuditLog
```

ilişkilerine sahiptir.

---

# Bilinen Durumlar ve Geliştirme Notları

Bu bölüm özellikle mevcut proje kodunun gerçek durumunu açıklar.

## 1. `.env.example` eksik

Arşivde `.env` bulunurken `.env.example` bulunmamaktadır.

Yeni geliştirici kurulumu için güvenli bir `.env.example` dosyası oluşturulması önerilir.

---

## 2. VakıfBank SOAP çağrısı tamamlanmamış

`VakifbankSyncService::sync()` canlı bankadan hareket çekmek yerine şu anda entegrasyon kontrolünün çalıştırıldığını kaydetmektedir.

Gerçek entegrasyon için:

```text
SOAP client
↓
VakıfBank response
↓
BankTransaction mapping
↓
duplicate/idempotency kontrolü
↓
eşleştirme
↓
Payment oluşturma
↓
AuditLog
```

akışının tamamlanması gerekir.

---

## 3. Telegram gönderimi bulunmuyor

Telegram bot ayarları kaydediliyor ancak mevcut arşivde gerçek Telegram mesaj gönderme servisi bulunmamaktadır.

---

## 4. Yetkilendirme daha da sıkılaştırılabilir

Rol alanı:

```text
admin
site_manager
```

olarak tutuluyor.

Bununla birlikte üretim seviyesinde:

- Laravel Policies
- Gates
- site kapsamı middleware'i
- action bazlı izinler

eklenmesi tavsiye edilir.

Özellikle site yöneticisinin başka bir siteye ait ID'leri URL üzerinden kullanmasını engelleyen merkezi bir authorization katmanı faydalı olacaktır.

---

## 5. Test kapsamı genişletilmeli

Mevcut testler temel özellikleri kapsıyor.

Özellikle şu modüller için Feature Test eklenmesi önerilir:

```text
Site CRUD
Apartment CRUD
Resident CRUD
Due CRUD
Payment CRUD
Income CRUD
Expense CRUD
Announcement
Owner report token
Report PDF
Database backup/restore
Authority
VakifBank transaction approval
Authorization / site isolation
```

---

# Sorun Giderme

## `APP_KEY` hatası

```bash
php artisan key:generate
```

---

## Migration hatası

Önce:

```bash
php artisan config:clear
```

ardından:

```bash
php artisan migrate
```

Geliştirme veritabanını sıfırlamak gerekiyorsa:

```bash
php artisan migrate:fresh --seed
```

> `migrate:fresh --seed` mevcut verileri siler. Üretimde kullanılmamalıdır.

---

## Cache temizleme

```bash
php artisan optimize:clear
```

Ardından:

```bash
php artisan optimize
```

---

## Vite manifest hatası

Örneğin:

```text
Vite manifest not found
```

görülürse:

```bash
npm install
npm run build
```

---

## CSS/JS güncellenmiyor

Geliştirme:

```bash
npm run dev
```

Production:

```bash
npm run build
```

Ardından Laravel cache:

```bash
php artisan optimize:clear
```

---

## Mail gönderilmiyor

`.env` içindeki:

```env
MAIL_MAILER=log
```

ayarını gerçek SMTP sağlayıcınıza göre değiştirin.

Sonrasında:

```bash
php artisan optimize:clear
```

---

## Scheduler çalışmıyor

Manuel test:

```bash
php artisan schedule:run
```

Cron:

```bash
* * * * * cd /var/www/site360pro && php artisan schedule:run >> /dev/null 2>&1
```

---

# Geliştirme İçin Önerilen İş Akışı

```bash
composer install
npm install
php artisan migrate --seed
npm run dev
php artisan serve
```

Ayrı terminalde:

```bash
php artisan queue:work
```

Scheduler için:

```bash
php artisan schedule:work
```

---

# Production Kontrol Listesi

Kurulumdan sonra:

- [ ] PHP 8.3+ kuruldu
- [ ] Composer kuruldu
- [ ] Node.js 20.19+ veya 22.12+ kuruldu
- [ ] `.env` hazırlandı
- [ ] `APP_KEY` oluşturuldu
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` doğru
- [ ] Veritabanı oluşturuldu
- [ ] Migration çalıştırıldı
- [ ] İlk admin hesabı güvenli hale getirildi
- [ ] Mail SMTP ayarları yapıldı
- [ ] `npm run build` çalıştırıldı
- [ ] Web root `/public` olarak ayarlandı
- [ ] HTTPS aktif
- [ ] Queue worker yapılandırıldı
- [ ] Scheduler cron yapılandırıldı
- [ ] Düzenli veritabanı yedeği ayarlandı
- [ ] Yedekten geri yükleme test edildi
- [ ] Banka entegrasyonu kullanılacaksa gerçek SOAP entegrasyonu doğrulandı
- [ ] Yetki izolasyonu test edildi
- [ ] Testler çalıştırıldı

---

# Lisans

Proje arşivinde açık bir `LICENSE` dosyası bulunmuyorsa, dağıtım lisansını varsaymayın.

Proje özel kullanım için geliştiriliyorsa kurum/proje sahibinin lisans ve dağıtım şartları ayrıca belirlenmelidir.

---

# Kısa Kurulum Özeti

En kısa kurulum akışı:

```bash
# 1) PHP bağımlılıkları
composer install

# 2) .env dosyasını hazırlayın
# APP_KEY, DB_* ve APP_URL ayarlarını yapın

# 3) Uygulama anahtarı
php artisan key:generate

# 4) Veritabanı
php artisan migrate --seed

# 5) Frontend
npm install
npm run build

# 6) Uygulama
php artisan serve
```

Ardından:

```text
http://127.0.0.1:8000/login
```

adresinden giriş yapılabilir.

Geliştirme seed'i kullanıldıysa ilk kullanıcı:

```text
E-posta: admin@admin.local
Şifre:   admin123
```

olacaktır.

**Üretim sisteminde bu bilgileri değiştirmeden uygulamayı internete açmayın.**
