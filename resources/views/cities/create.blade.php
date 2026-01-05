@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Új város hozzáadása</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('cities.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Város neve <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="pl. Budapest"
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
                                    <option value="{{ $county['id'] }}" {{ old('county_id') == $county['id'] ? 'selected' : '' }}>
                                        {{ $county['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('county_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="postal_code" class="form-label">Irányítószám <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('postal_code') is-invalid @enderror" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="{{ old('postal_code') }}"
                                   placeholder="pl. 1011"
                                   maxlength="4"
                                   pattern="[0-9]{4}"
                                   required>
                            <small class="form-text text-muted">4 számjegyű irányítószám</small>
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <strong>Megjegyzés:</strong> A város létrehozásakor meg kell adni a megyét és az irányítószámot is.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('cities.index') }}" class="btn btn-secondary">
                                Vissza
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Létrehozás
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('postal_code').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);
    });
</script>
@endpush