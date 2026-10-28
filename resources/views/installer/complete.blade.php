@extends('installer.layout')
@section('title', 'Kurulum Tamamlandı')

@section('content')
    <div class="installer-done">
        <span class="installer-done-icon"><x-icon name="circle-check" /></span>
        <h2>Kurulum tamamlandı</h2>
        <p class="text-secondary">{{ $result['site_name'] }} sitesi için {{ $result['blocks'] }} blok ve {{ $result['apartments'] }} daire oluşturuldu. Yönetici hesabınız ({{ $result['admin_email'] }}) hazır.</p>
        <a class="btn btn-primary btn-lg" href="{{ route('login') }}"><x-icon name="login" class="me-1" /> Giriş Yap</a>
    </div>
    @unless ($result['lock_written'])
        <div class="alert alert-warning mt-4"><x-icon name="alert-triangle" class="me-2" />Kurulum kilit dosyası (storage/app/installed.lock) yazılamadı. Sihirbaz, yönetici kaydı bulunduğu için yine de kapalı kalır; ancak klasör izinlerini düzeltmeniz önerilir.</div>
    @endunless
    <div class="installer-group mt-4">
        <h3 class="installer-group-title"><x-icon name="list-check" class="me-1" /> Sonraki Adımlar</h3>
        <ol class="installer-next">
            <li><strong>Zamanlanmış görev ekleyin.</strong> Banka hareketi kontrolü gibi otomatik işler için sunucunuzun cron / Görev Zamanlayıcı'sına şunu ekleyin:<code>* * * * * cd {{ $basePath }} && php artisan schedule:run >> /dev/null 2>&1</code></li>
            <li><strong>Kuyruk çalışanı.</strong> Kuyruk sürücüsü "database" ise <code>php artisan queue:work</code> komutunu bir süreç yöneticisiyle çalıştırın.</li>
            <li><strong>Yedek alın.</strong> Giriş yaptıktan sonra Ayarlar &rarr; Veritabanı ekranından düzenli JSON yedeği indirin.</li>
            <li><strong>Performans (isteğe bağlı).</strong> Canlı ortamda <code>php artisan config:cache &amp;&amp; php artisan route:cache &amp;&amp; php artisan view:cache</code> çalıştırabilirsiniz.</li>
            <li><strong>Adres.</strong> Panel <code>{{ $appUrl }}</code> adresinde çalışacaktır; farklı bir adres kullanıyorsanız .env dosyasındaki <code>APP_URL</code> değerini güncelleyin.</li>
        </ol>
    </div>
@endsection
