@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Város szerkesztése</h1>

    {{-- HIBAÜZENETEK MEGJELENÍTÉSE --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ADATELŐKÉSZÍTÉS --}}
    @php
        // Megpróbáljuk kinyerni a megye ID-t biztonságosan.
        // Ha nincs 'county_id', megnézzük a 'county' objektumon belül.
        $currentCountyId = $city['county_id'] ?? $city['county']['id'] ?? null;
    @endphp

    <form action="{{ route('cities.update', $city['id']) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Város neve:</label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="{{ old('name', $city['name']) }}" required>
        </div>

        <div class="mb-3">
            <label for="county_id" class="form-label">Megye:</label>
            <select name="county_id" id="county_id" class="form-select" required>
                <option value="">Válassz megyét...</option>
                @foreach($counties as $county)
                    <option value="{{ $county['id'] }}" 
                        {{-- Itt használjuk a biztonságos változót az összehasonlításhoz --}}
                        {{ $currentCountyId == $county['id'] ? 'selected' : '' }}>
                        {{ $county['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Mentés</button>
        <a href="{{ route('cities.index') }}" class="btn btn-secondary">Mégse</a>
    </form>
</div>
@endsection