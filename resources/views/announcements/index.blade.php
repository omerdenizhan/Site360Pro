@extends('layouts.app', ['title' => 'Duyurular - Site360Pro'])

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-3 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Site iletişimi</div>
                    <h2 class="page-title">Duyurular</h2>
                    <div class="text-secondary mt-1">Sakinlere gösterilecek yönetim notları ve bilgilendirmeler.</div>
                </div>
                <div class="col-auto"><a class="btn btn-primary" href="{{ route('announcements.create') }}"><x-icon name="plus" class=" me-1" /> Duyuru Ekle</a></div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (session('status'))
                <div class="alert alert-success"><x-icon name="circle-check" class=" me-2" />{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger"><x-icon name="alert-circle" class="me-2" />{{ $errors->first() }}</div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Duyuru Listesi</h3>
                </div>
                <div class="card-body border-bottom">
                    <form class="d-flex justify-content-between align-items-end w-100" method="get" action="{{ route('announcements.index') }}">
                        <div class="col-3 col-md-3 col-lg-3">
                            <label class="form-label">Site</label>
                            <select class="form-select" name="site_id">
                                @foreach ($sites as $filterSite)
                                    <option value="{{ $filterSite->id }}" @selected($site->id === $filterSite->id)>{{ $filterSite->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-1 col-md-1 col-lg-1">
                            <label class="form-label">Yıl</label>
                            <input class="form-control" type="number" name="year" value="{{ $year }}" min="2020" max="2100">
                        </div>
                        <div class="col-2 col-md-2 col-lg-2">
                            <label class="form-label">Ay</label>
                            <select class="form-select" name="month">
                                <option value="">Tüm aylar</option>
                                @foreach ($months as $number => $name)
                                    <option value="{{ $number }}" @selected($month === $number)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-5 col-md-5 col-lg-5">
                            <label class="form-label">Arama</label>
                            <input class="form-control" name="search" value="{{ $search }}" placeholder="Başlık veya içerik">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" type="submit"><x-icon name="filter"/></button>
                        </div>
                    </form>
                </div>
                <div class="list-group list-group-flush">
                    @forelse ($announcements as $announcement)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto"><span class="avatar bg-primary-lt"><x-icon name="speakerphone" class="" /></span></div>
                                <div class="col">
                                    <div class="fw-bold">{{ $announcement->title }}</div>
                                    <div class="text-secondary">{{ $announcement->content }}</div>
                                </div>
                                <div class="col-auto text-secondary">{{ $announcement->publish_date->format('d.m.Y') }}</div>
                                <div class="col-auto">
                                    <div class="btn-list flex-nowrap">
                                        <a class="btn btn-sm" href="{{ route('announcements.send', $announcement) }}"><x-icon name="send" class="me-1" /> Gönder</a>
                                        <a class="btn btn-sm" href="{{ route('announcements.edit', $announcement) }}">Düzenle</a>
                                        <form method="post" action="{{ route('announcements.destroy', $announcement) }}" data-confirm="Duyuru silinsin mi?">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-secondary py-5">Duyuru bulunamadı.</div>
                    @endforelse
                </div>
                <div class="card-footer">{{ $announcements->links() }}</div>
            </div>
        </div>
    </div>
@endsection
