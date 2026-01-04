@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Megye szerkesztése</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('counties.update', $county['id']) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Megye neve <span class="text-danger">*</span></label>
                            <input type="text" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name', $county['name'] ?? $county->name ?? '') }}"
                                    required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('counties.index') }}" class="btn btn-secondary">
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