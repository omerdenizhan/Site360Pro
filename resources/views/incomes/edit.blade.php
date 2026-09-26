@extends('layouts.app', ['title' => 'Gelir Düzenle - Site360Pro'])

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-3 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Kasa yönetimi</div>
                    <h2 class="page-title">Gelir Düzenle</h2>
                    <div class="text-secondary mt-1">{{ $income->description }}</div>
                </div>
                <div class="col-auto"><a class="btn" href="{{ route('incomes.index') }}"><x-icon name="arrow-left" class="me-1" /> Listeye Dön</a></div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <form class="card" method="post" action="{{ route('incomes.update', $income) }}">
                @csrf
                @method('put')
                <div class="card-header"><h3 class="card-title">Gelir Bilgileri</h3></div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger"><x-icon name="alert-circle" class="me-2" />{{ $errors->first() }}</div>
                    @endif
                    <div class="row g-3 align-items-end">
                        <div class="col-2 col-md-2 col-lg-2">
                            <label class="form-label">Kategori</label>
                            <select class="form-select" name="category">
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category', $income->category) === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2">
                            <label class="form-label">Tutar</label>
                            <div class="input-group">
                                <input class="form-control" type="number" name="amount" min="0.01" step="0.01" value="{{ old('amount', $income->amount) }}" required>
                                <span class="input-group-text">₺</span>
                            </div>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2">
                            <label class="form-label">Tarih</label>
                            <input class="form-control" type="date" name="income_date" value="{{ old('income_date', $income->income_date->toDateString()) }}" required>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <label class="form-label">Açıklama</label>
                            <input class="form-control" name="description" value="{{ old('description', $income->description) }}" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a class="btn" href="{{ route('incomes.index') }}">Vazgeç</a>
                    <button class="btn btn-primary" type="submit"><x-icon name="check" class="me-1" /> Güncelle</button>
                </div>
            </form>
        </div>
    </div>
@endsection
