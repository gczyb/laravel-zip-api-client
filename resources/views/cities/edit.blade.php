@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Város szerkesztése</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('cities.update', $city['id']) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Város neve <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $city['name']) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="county_id" class="form-label">Megye <span class="text-danger">*</span></label>
                            <select class="form-select @error('county_id') is-invalid @enderror" 
                                    id="county_id" 
                                    name="county_id" 
                                    required>
                                <option value="">-- Válassz megyét --</option>
                                @foreach($counties as $county)
                                    <option value="{{ $county['id'] }}" 
                                            {{ old('county_id', $city['county_id']) == $county['id'] ? 'selected' : '' }}>
                                        {{ $county['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('county_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(isset($city['postal_codes']) && count($city['postal_codes']) > 0)
                            <div class="alert alert-info">
                                <strong>Jelenlegi irányítószám:</strong> 
                                @foreach($city['postal_codes'] as $postalCode)
                                    <span class="badge bg-secondary">{{ $postalCode['code'] }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('cities.index') }}" class="btn btn-secondary">
                                Vissza
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Frissítés
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection