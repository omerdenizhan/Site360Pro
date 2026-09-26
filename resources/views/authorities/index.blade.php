@extends('layouts.app', ['title' => 'Yetkililer - Site360Pro'])

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-3 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Erişim yönetimi</div>
                    <h2 class="page-title">Yetkililer</h2>
                    <div class="text-secondary mt-1">Yetkili kullanıcılarını ve daire sahiplerine gönderilecek süreli rapor bağlantılarını yönetin.</div>
                </div>
                <div class="col-auto">
                    <a class="btn btn-primary" href="{{ route('authorities.create') }}"><x-icon name="user-cog" class="me-1" /> Yetkili Ekle</a>
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

            <div class="card mb-3">
                <div class="card-body">
                    <form class="d-flex justify-content-between align-items-end w-100" method="get" action="{{ route('authorities.index') }}">
                        <div class="col-4 col-md-4 col-lg-4">
                            <label class="form-label">Site</label>
                            <select class="form-select" name="site_id" onchange="this.form.submit()">
                                @foreach ($sites as $filterSite)
                                    <option value="{{ $filterSite->id }}" @selected($site && $filterSite->is($site))>{{ $filterSite->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-7 col-md-7 col-lg-7">
                            <label class="form-label">Blok</label>
                            <select class="form-select" name="block_id">
                                <option value="">Tüm bloklar</option>
                                @foreach ($blocks as $block)
                                    <option value="{{ $block->id }}" @selected($blockId === $block->id)>{{ $block->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" type="submit"><x-icon name="filter"/></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row row-cards">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Panel Kullanıcıları</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Kullanıcı</th>
                                        <th>Yetki</th>
                                        <th>Site</th>
                                        <th>Durum</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <div class="text-secondary small">{{ $user->email }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $user->role === 'admin' ? 'bg-blue-lt' : 'bg-indigo-lt' }}">
                                                {{ $user->role === 'admin' ? 'Genel yönetici' : 'Site yöneticisi' }}
                                            </span>
                                        </td>
                                        <td>{{ $user->site?->name ?? 'Tüm siteler' }}</td>
                                        <td>
                                            <span class="badge {{ $user->is_active ? 'bg-green-lt' : 'bg-red-lt' }}">
                                                {{ $user->is_active ? 'Aktif' : 'Pasif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                <a class="btn btn-sm" href="{{ route('authorities.edit', $user) }}"><x-icon name="edit" class="me-1" /> Düzenle</a>
                                                <form method="post" action="{{ route('authorities.destroy', $user) }}" data-confirm="Yetkili hesabı silinsin mi?">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn btn-sm btn-outline-danger" type="submit"><x-icon name="trash" class="me-1" /> Sil</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-secondary py-5">Yetkili bulunamadı.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">{{ $users->links() }}</div>
                    </div>
                </div>

                
            </div>
        </div>
    </div>
@endsection
