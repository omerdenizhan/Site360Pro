@extends('layouts.app', ['title' => 'Yeni Sakin - Site360Pro'])

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-3 align-items-center">
                <div class="col">
                    <div class="page-pretitle">Kişi yönetimi</div>
                    <h2 class="page-title">Yeni Sakin Ekle</h2>
                    <div class="text-secondary mt-1">Seçilen dairede önceki aktif sakin pasife alınır.</div>
                </div>
                <div class="col-auto"><a class="btn" href="{{ route('residents.index') }}"><x-icon name="arrow-left" class=" me-1" /> Listeye Dön</a></div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <form class="card" method="post" action="{{ route('residents.store') }}">
                @csrf
                <div class="card-header"><h3 class="card-title">Sakin Bilgileri</h3></div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger"><x-icon name="alert-circle" class=" me-2" />{{ $errors->first() }}</div>
                    @endif
                    <div class="row g-3 align-items-end">
                        <div class="col-8 col-md-8 col-lg-8">
                            <label class="form-label">Daire</label>
                            <select class="form-select" name="apartment_id" required>
                                @foreach ($apartments as $apartment)
                                    <option value="{{ $apartment->id }}" @selected(old('apartment_id') == $apartment->id)>
                                        {{ $apartment->buildingBlock->name }} / {{ $apartment->number }}{{ $apartment->activeResident ? ' - ' . $apartment->activeResident->full_name : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4 col-md-4 col-lg-4">
                            <label class="form-label">Adı Soyadı</label>
                            <input class="form-control" name="full_name" value="{{ old('full_name') }}" required>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-2 col-md-2 col-lg-2">
                            <label class="form-label">Tip</label>
                            <select class="form-select" name="resident_type">
                                <option value="owner" @selected(old('resident_type', 'owner') === 'owner')>Ev sahibi</option>
                                <option value="tenant" @selected(old('resident_type') === 'tenant')>Kiracı</option>
                            </select>
                        </div>
                        <div class="col-2 col-md-2 col-lg-2">
                            <label class="form-label">Telefon</label>
                            <input class="form-control" name="phone" value="{{ old('phone') }}" placeholder="05XX XXX XX XX">
                        </div>
                        <div class="col-3 col-md-3 col-lg-3">
                            <label class="form-label">E-posta</label>
                            <input class="form-control" type="email" name="email" value="{{ old('email') }}">
                        </div>
                        <div class="col-5 col-md-5 col-lg-5">
                            <label class="form-label">Görev Yeri</label>
                            <input class="form-control" name="workplace" value="{{ old('workplace') }}">
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row g-3 align-items-end justify-content-between">
                        <div class="col-4 col-md-4 col-lg-4">
                            <label class="form-label">Araç Cinsi 1</label>
                            <input class="form-control" name="vehicle_model_1" value="{{ old('vehicle_model_1') }}">
                        </div>
                        <div class="col-2 col-md-2 col-lg-2">
                            <label class="form-label">Araç Plakası 1</label>
                            <input class="form-control" name="vehicle_plate_1" value="{{ old('vehicle_plate_1') }}">
                        </div>
                        <div class="col-4 col-md-4 col-lg-4">
                            <label class="form-label">Araç Cinsi 2</label>
                            <input class="form-control" name="vehicle_model_2" value="{{ old('vehicle_model_2') }}">
                        </div>
                        <div class="col-2 col-md-2 col-lg-2">
                            <label class="form-label">Araç Plakası 2</label>
                            <input class="form-control" name="vehicle_plate_2" value="{{ old('vehicle_plate_2') }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a class="btn" href="{{ route('residents.index') }}">Vazgeç</a>
                    <button class="btn btn-primary" type="submit"><x-icon name="check" class=" me-1" /> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
@endsection
