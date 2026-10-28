@extends('installer.layout')
@section('title', 'Gereksinimler')
@section('heading', 'Sistem Gereksinimleri')

@section('content')
    <p class="text-secondary">Kuruluma başlamadan önce sunucunuzun Site360Pro'yu çalıştırmaya uygun olup olmadığı denetlenir. Kırmızı işaretli maddeler giderilmeden devam edilemez.</p>
    @if ($report['passes'])
        <div class="alert alert-success"><x-icon name="circle-check" class="me-2" />Tüm zorunlu gereksinimler karşılanıyor.@if ($report['warnings'] > 0) {{ $report['warnings'] }} uyarı var; bunlar kurulumu engellemez.@endif</div>
    @else
        <div class="alert alert-danger"><x-icon name="alert-circle" class="me-2" />{{ $report['failed'] }} zorunlu gereksinim karşılanmıyor. Düzeltip sayfayı yenileyin.</div>
    @endif
    @php
        $groups = collect($report['groups'])->keyBy('title');
        $merged = [
            [
                'icon' => 'server',
                'title' => 'PHP, Veritabanı ve Önerilen Eklentiler',
                'sections' => ['PHP Sürümü ve Ayarları', 'Veritabanı Sürücüleri', 'Önerilen Eklentiler'],
            ],
            [
                'icon' => 'puzzle',
                'title' => 'Zorunlu Eklentiler ve Dosya İzinleri',
                'sections' => ['Zorunlu PHP Eklentileri', 'Klasör ve Dosya İzinleri'],
            ],
        ];
    @endphp
    <div class="row g-3">
        @foreach ($merged as $box)
            <div class="col-12 col-lg-6">
                <div class="installer-group">
                    <h3 class="installer-group-title"><x-icon :name="$box['icon']" class="me-1" /> {{ $box['title'] }}</h3>
                    @foreach ($box['sections'] as $sectionTitle)
                        @if ($group = $groups->get($sectionTitle))
                            <h4 class="installer-subgroup-title"><x-icon :name="$group['icon']" class="me-1" /> {{ $group['title'] }}</h4>
                            <ul class="installer-checks">
                                @foreach ($group['items'] as $item)
                                    <li class="is-{{ $item['status'] }}">
                                        <span class="installer-check-icon">
                                            <x-icon :name="$item['status'] === 'ok' ? 'circle-check' : ($item['status'] === 'warn' ? 'alert-triangle' : 'circle-x')" />
                                        </span>
                                        <span class="installer-check-text">
                                            <strong>{{ $item['label'] }}</strong>
                                            <small>{{ $item['detail'] }}</small>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <form method="post" action="{{ route('install.store', ['step' => 'requirements']) }}" class="mt-4">
        @csrf
        <div class="installer-actions">
            <a class="btn btn-outline-secondary" href="{{ route('install.step', ['step' => 'requirements']) }}"><x-icon name="refresh" class="me-1" /> Yeniden Kontrol Et</a>
            <button class="btn btn-primary" type="submit" @disabled(! $report['passes'])>Devam Et <x-icon name="arrow-right" class="ms-1" /></button>
        </div>
    </form>
@endsection
