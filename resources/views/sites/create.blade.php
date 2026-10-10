@extends('layouts.app', ['title' => 'Yeni Site - Site360Pro'])

@php
    $blockValues = old('blocks', ['', '', '']);
@endphp

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-3 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Site yönetimi</div>
                    <h2 class="page-title">Yeni Site Ekle</h2>
                    <div class="text-secondary mt-1">Site adı, adresi ve bloklarını kaydedin.</div>
                </div>
                <div class="col-auto"><a class="btn" href="{{ route('sites.index') }}"><x-icon name="arrow-left" class="me-1" /> Listeye Dön</a></div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <form class="card" method="post" action="{{ route('sites.store') }}">
                @csrf
                <div class="card-header"><h3 class="card-title">Site Bilgileri</h3></div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger"><x-icon name="alert-circle" class="me-2" />{{ $errors->first() }}</div>
                    @endif
                    <div class="row g-3 align-items-end">
                        <div class="col-4 col-md-4 col-lg-4">
                            <label class="form-label">Site Adı</label>
                            <input class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-8 col-md-8 col-lg-8">
                            <label class="form-label">Adres</label>
                            <input class="form-control" name="address" value="{{ old('address') }}">
                        </div>
                        <div class="col-2 col-md-2 col-lg-2">
                            <label class="form-label">İlk Kurulum Kasa Devri (₺)</label>
                            <input class="form-control" type="number" step="0.01" name="opening_balance" value="{{ old('opening_balance', '0') }}" required>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2">
                            <label class="form-label">Devir Tarihi</label>
                            <input class="form-control" type="date" name="opening_balance_date" value="{{ old('opening_balance_date') }}" required>
                        </div>
                        <div class="col-8 col-md-8 col-lg-8">
                            <div class="form-hint">Programa başlarken kasada bulunan tutar. Kasa hesabı devir tarihinin ait olduğu ayın başından itibaren yapılır; öncesindeki kayıtlar kasaya dahil edilmez.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Bloklar</label>
                            <div class="vstack gap-2" data-block-list>
                                @foreach ($blockValues as $blockValue)
                                    <div class="input-group">
                                        <span class="input-group-text"><x-icon name="building" /></span>
                                        <input class="form-control" name="blocks[]" value="{{ $blockValue }}" placeholder="Örn: A Blok">
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn btn-sm mt-2" type="button" data-add-block>
                                <x-icon name="plus" class="me-1" /> Blok Satırı Ekle
                            </button>
                            <div class="form-hint">Her bloğu ayrı satıra yazın. Virgül kullanmanıza gerek yok.</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a class="btn" href="{{ route('sites.index') }}">Vazgeç</a>
                    <button class="btn btn-primary" type="submit"><x-icon name="check" class="me-1" /> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
@endsection
