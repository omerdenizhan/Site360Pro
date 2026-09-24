# 🏢 Site ve Apartman Yönetim Sistemi (Laravel)

Bu proje; birden fazla site/blok yapısını, daire sakinlerini, aidat ve finansal gelir/gider takiplerini ve banka entegrasyonlarını tek bir platform üzerinden yönetmek amacıyla geliştirilmiş kapsamlı bir **Laravel tabanlı Site Yönetim Otomasyonudur**.

---

## 🚀 Proje Özellikleri

### 1. 🏢 Site ve Blok Yönetimi
* **Çoklu Site & Blok Yapısı:** Farklı siteleri, bu sitelere bağlı blokları (`BuildingBlock`) ve bağımsız bölümleri/daireleri (`Apartment`) esnek bir şekilde tanımlama.
* **Daire Yönetimi:** Dairelerin kat numarası, kapı numarası, doluluk durumu ve ait olduğu site/blok bazlı eşleştirilmesi.

### 2. 👥 Sakin & Kat Maliki Yönetimi
* **Sakin Prototipi (`Resident`):** Daire sakinlerinin (Kiracı / Ev Sahibi) kişisel bilgileri, iletişim bilgileri ve daire ilişkileri.
* **Kat Maliki Özel Raporlama:** Mülk sahiplerine özel üretilen erişim bağlantıları (`OwnerReportLink`) ile mülk sahiplerinin kendi finansal durumlarını anlık inceleyebilmesi.

### 3. 💰 Finansal Yönetim & Muhasebe
* **Aidat Takibi (`Due`):** Daire bazlı tanımlanan rutin veya dönemsel aidatlar.
* **Gelirler (`Income`) & Giderler (`Expense`):** Kategori bazlı gelir/gider kayıtları, fatura/makbuz takipleri.
* **Tahsilat & Ödemeler (`Payment`):** Yapılan tahsilatların sisteme işlenmesi, makbuz/makbuz PDF çıktısı alma.

### 4. 🏦 Banka Entegrasyonu (VakıfBank)
* **Otomatik Hesap Hareketleri Senkronizasyonu (`VakifbankSyncService`):** Banka hesap hareketlerini otomatik çekerek ödemelerle eşleştirme.
* **Banka Hareket Kayıtları (`BankIntegration` & `BankTransaction`):** Gelen havale/EFT işlemlerini otomatik analiz etme ve ilgili daire/sakin ile ilişkilendirme.

### 5. 📢 Duyurular & İletişim
* **Sistem İçi Duyuru Yayını (`Announcement`):** Tüm sakinlere veya belirli blok/siteye yönelik duyurular oluşturma ve e-posta ile toplu duyuru iletimi.

### 6. 🛠️ Güvenlik & Denetim (Audit Logs)
* **Sistem Denetim Kayıtları (`AuditLog`):** Yapılan kritik işlemlerin (Ekleme, Güncelleme, Silme) kimin tarafından, ne zaman gerçekleştirildiğini kayıt altına alma.
* **Rol & Yetkilendirme (`AuthorityController`):** Kullanıcı rolleri ve yetki sınırlandırmaları.

---

## 🛠️ Teknik Özellikler & Teknoloji Yığını

* **Backend Framework:** PHP 8.2+ / Laravel 11.x
* **Frontend / UI:** Blade Templating, Tailwind CSS, Vite
* **Veritabanı:** MySQL / SQLite
* **İkon / UI Bileşenleri:** Tabler Icons
* **PDF Oluşturucu:** DomPDF / Laravel-PDF Entegrasyonu (Makbuz ve Rapor çıktıları için)
* **Servis Yapısı:** Servis odaklı mimari (Örn: `VakifbankSyncService`)

---

## 📂 Proje Dizin Yapısı

```text
├── app/
│   ├── Http/Controllers/       # Controller sınıfları (Site, Resident, Payment, Due vb.)
│   ├── Models/                 # Eloquent Veritabanı Modelleri
│   ├── Services/               # İş mantığı servisleri (VakifbankSyncService vb.)
│   └── Providers/              # Servis Sağlayıcıları
├── config/                     # Uygulama ve entegrasyon yapılandırmaları
├── database/
│   ├── migrations/             # Veritabanı tablo şemaları
│   ├── seeders/                # Örnek veri doldurucular
│   └── database.sqlite         # Varsayılan SQLite veritabanı (Opsiyonel)
├── public/                     # Derlenmiş CSS/JS assetleri ve index.php
├── resources/
│   ├── views/                  # Blade şablonları (Site, Daire, Aidat, Ödeme vb.)
│   ├── css/                    # Tailwind / Uygulama CSS dosyaları
│   └── js/                     # JavaScript modülleri
└── routes/                     # Web ve konsol rotaları
```

---

## ⚙️ Kurulum Aşamaları

Projeyi yerel ortamınızda çalıştırmak için aşağıdaki adımları sırasıyla takip edebilirsiniz:

### 1. Gereksinimler
* **PHP:** >= 8.2
* **Composer:** >= 2.0
* **Node.js & NPM:** >= 18.x
* **Veritabanı:** MySQL / PostgreSQL veya SQLite

---

### 2. Depoyu Klonlayın ve Klasöre Geçin
```bash
git clone <repo-url>
cd <proje-klasor-adi>
```

---

### 3. PHP Bağımlılıklarını Yükleyin
```bash
composer install
```

---

### 4. Çevre (.env) Dosyasını Hazırlayın
`.env.example` dosyasını kopyalayarak `.env` dosyanızı oluşturun:
```bash
cp .env.example .env
```
Uygulama anahtarını (APP_KEY) oluşturun:
```bash
php artisan key:generate
```

---

### 5. Veritabanı Yapılandırması

`.env` dosyanızda veritabanı ayarlarınızı güncelleyin. 

**SQLite kullanmak isterseniz:**
```env
DB_CONNECTION=sqlite
# DB_DATABASE ayarını kaldırabilir veya varsayılan bırakabilirsiniz.
```

**MySQL kullanmak isterseniz:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=site_yonetimi
DB_USERNAME=root
DB_PASSWORD=
```

---

### 6. Veritabanı Migrasyonlarını Çalıştırın
Tabloları ve (varsa) başlangıç verilerini veritabanına yükleyin:
```bash
php artisan migrate --seed
```

---

### 7. Frontend Bağımlılıklarını Yükleyin ve Derleyin
```bash
npm install
npm run build
# Geliştirme ortamı için canlı derleme yapacaksanız:
# npm run dev
```

---

### 8. Uygulamayı Başlatın
Laravel dahili geliştirme sunucusunu çalıştırın:
```bash
php artisan serve
```

Tarayıcınızdan `http://127.0.0.1:8000` adresine giderek uygulamaya erişebilirsiniz.

---

## 🔌 Banka Entegrasyon Yapılandırması

VakıfBank hesap hareketleri entegrasyonunu aktif etmek için aşağıdaki adımları tamamlayın:

1. `config/services.php` veya `.env` içerisine VakıfBank API/Servis erişim bilgilerini ekleyin.
2. Servisi manuel tetiklemek için ilgili Controller / Cron Job yapılandırmasını kullanın:
   ```bash
   php artisan schedule:run
   ```

---

## 📄 Lisans

Bu proje özel mülkiyettedir / açık kaynak lisansı detayları için `LICENSE` dosyasına göz atabilirsiniz.