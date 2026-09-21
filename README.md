# Site360Pro

Site360Pro, apartman ve site yönetimi için geliştirilmiş Laravel tabanlı bir yönetim panelidir. Site, blok, daire, sakin, aidat, ödeme, gider, duyuru ve banka entegrasyonu işlemlerini tek bir merkezden yönetmeye olanak tanır.

Proje, site yöneticilerinin aidat takiplerini kolaylaştırmak, ödemeleri izlemek, gelir-gider dengesini görmek ve sahiplere özel raporlar sunmak amacıyla tasarlanmıştır.

## Özellikler

- Çoklu site yönetimi
- Blok ve daire yapısı
- Sakin / oturan bilgileri takibi
- Aidat oluşturma ve ödeme takibi
- Kısmi/full/ödenmemiş aidat durumu
- Gelir ve gider takibi
- Duyuru yayınlama ve e-posta gönderimi
- Telegram bildirimleri
- Mülkiyet sahipleri için özel rapor bağlantıları
- Vakıfbank banka hareketi entegrasyonu altyapısı
- PDF aidat/fiş çıktıları
- Yönetici işlemleri için audit log takibi
- Modern UI (Tabler + Vite + Tailwind)

## Teknoloji Yığını

- PHP 8.3
- Laravel 13
- SQLite varsayılan veritabanı desteği
- Vite + Tailwind CSS
- Composer
- NPM
- Dompdf (PDF oluşturma)

## Proje Yapısı

```text
.
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Providers/
│   └── Services/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── json/
├── lang/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
├── storage/
├── tests/
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
├── vite.config.js
├── .env
├── .gitignore
└── README.md
```

## Ana Modüller

### 1. Dashboard
Ana ekranda:

- seçili site ve blok bazlı görünüm
- toplam daire durumu
- aidat özetleri
- koleksiyon oranı
- günün ödemeleri
- son ödemeler
- giderler ve duyurular

### 2. Site ve Yapı Yönetimi
- site ekleme/düzenleme
- blok ekleme/düzenleme
- apartman yapısı yönetimi

### 3. Konut / Sakin Yönetimi
- daire bilgileri
- aktif sakin eşleştirmeleri
- iletişim bilgileri

### 4. Aidatlar ve Ödemeler
- dönemsel aidat oluşturma
- ödeme kaydı ekleme
- kısmi ödemeler
- fatura/fiş görünümü ve PDF üretimi

### 5. Gider Yönetimi
- elektrik, su, bakım, personel, temizlik vb. gider kayıtları
- site bazlı toplam gider takibi

### 6. Duyurular ve Raporlar
- site duyuruları
- e-posta gönderimi
- yöneticiler için rapor sayfası
- mülk sahibi özel rapor linkleri

### 7. Entegrasyonlar
- Vakıfbank hesap hareketleri için yapı mevcut
- manuel eşleştirme akışı ve finansal işlem bağlama
- otomatik senkronizasyon komutları

### 8. Ayarlar
- genel sistem ayarları
- Telegram bot ve kanal ayarları
- public URL / destek e-postası

## Gereksinimler

Aşağıdaki araçların sisteminizde kurulu olması gerekir:

- PHP 8.3+
- Composer
- Node.js 18+
- npm
- Git

## Kurulum

1. Projeyi klonlayın:

```bash
git clone <repository-url>
cd Site360Pro
```

2. PHP bağımlılıklarını kurun:

```bash
composer install
```

3. JavaScript bağımlılıklarını kurun:

```bash
npm install
```

4. Ortam değişkenlerini oluşturup düzenleyin:

Proje kökünde mevcut `.env` dosyası varsa kullanılabilir. Yoksa örnek bir dosya oluşturup gerekli değerleri ekleyin.

Örnek temel ayarlar:

```env
APP_NAME=Site360Pro
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/your/project/database/database.sqlite

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

> Not: Projede varsayılan veritabanı konfigürasyonu SQLite olarak ayarlanmıştır. Uygulamanın çalışması için SQLite dosya yolunun doğru olması gerekir.

5. Uygulama anahtarını oluşturun:

```bash
php artisan key:generate
```

6. Veritabanını oluşturup migrate edin:

```bash
php artisan migrate
```

7. Gerekirse örnek veriler eklemek için seed çalıştırabilirsiniz:

```bash
php artisan db:seed
```

8. Frontend kaynaklarını derleyin:

```bash
npm run build
```

## Çalıştırma

Geliştirme sunucusunu başlatmak için:

```bash
php artisan serve
```

Tarayıcıda şu URL'yi açın:

```text
http://localhost:8000
```

### Vite geliştirme sunucusu

Frontend değişikliklerini izlemek için:

```bash
npm run dev
```

### Tüm geliştirme araçlarını birlikte çalıştırma

Proje `composer.json` içinde tanımlı `dev` betiği kullanılır:

```bash
composer run dev
```

Bu komut şunları birlikte başlatır:

- Laravel sunucusu
- queue listener
- log izleyici
- Vite geliştirme sunucusu

## Testler

Projede PHPUnit kullanılmaktadır. Testleri çalıştırmak için:

```bash
php artisan test
```

## Önemli Artisan Komutları

### Banka senkronizasyonu

Vakıfbank entegrasyonu için manuel tetikleme:

```bash
php artisan bank:sync-vakifbank --site_id=1
```

### Zamanlanmış görevler

Zamanlayıcı, her dakika çalışacak şekilde konfigüre edilmiştir:

```bash
php artisan schedule:run
```

## Telegram ve Genel Ayarlar

Yönetim panelinden şu ayarlar düzenlenebilir:

- Telegram bot token
- chat ID
- bildirim aktif/pasif
- uygulama genel URL
- destek e-posta adresi

Bu ayarlar `SystemSetting` modeline kaydedilir ve uygulama içinde merkezi şekilde okunur.

## Vakıfbank Entegrasyonu

Proje içinde Vakıfbank için yapı mevcuttur:

- banka entegrasyonu kaydı
- hareket listesi
- eşleştirme ve manuel onay akışı
- otomatik senkronizasyon komudu

Şu anki `VakifbankSyncService` sürümü, canlı SOAP çağrısı yerine hazırlık ve validasyon mantığı içerir; gerçek banka SOAP entegrasyonu için servis tarafının tamamlanması gerekir.

## Güvenlik ve Audit Log

Uygulama işlemler sırasında aşağıdaki verileri izler:

- kullanıcı kimliği
- eylem adı
- tablo adı
- IP adresi
- açıklama

Bu sayede yöneticilerin hangi işlemleri yaptığını takip etmek mümkündür.

## Kullanım Akışı

1. Site oluşturulur.
2. Blok ve daire yapısı tanımlanır.
3. Sakinler kaydedilir.
4. Aidat dönemleri oluşturulur.
5. Ödemeler işlenir.
6. Giderler kaydedilir.
7. Duyurular yayınlanır.
8. Raporlar ve PDF çıktıları alınır.
9. Telegram veya banka işlemleri için entegrasyonlar aktif hale getirilir.

## Sorun Giderme

### SQLite hatası alıyorum

- `DB_DATABASE` değerinin doğru dosya yolunu gösterdiğinden emin olun.
- Veritabanı dosyasının yazılabilir olduğundan emin olun.

### Composer bağımlılık hatası

```bash
composer clear-cache
composer install
```

### Node bağımlılık hatası

```bash
rm -rf node_modules package-lock.json
npm install
```

### Uygulama çalışmıyor

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Katkıda Bulunma

1. Depoyu fork edin.
2. Yeni branch oluşturun.
3. Değişikliklerinizi yapın.
4. Testleri çalıştırın.
5. Pull request oluşturun.

## Lisans

Bu proje MIT lisansı altında sunulmaktadır.

## Not

Bu uygulama, yoğun olarak site ve apartman yönetimi için tasarlanmıştır. Özellikle aidat toplama, muhasebe benzeri kontrol ve bakım/operasyon takibi için uygun bir yönetim arayüzü sağlar. Gerçek banka SOAP uç noktasının bağlanması için ek güvenlik ve üretim parametreleri gerekecektir.
