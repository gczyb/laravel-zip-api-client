@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Város szerkesztése (JAVÍTVA)</h1>

    {{-- Hibák megjelenítése --}}
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

    {{-- Megye ID biztonságos kinyerése --}}
    @php
        $currentCountyId = null;
        if (isset($city['county_id'])) {
            $currentCountyId = $city['county_id'];
        } elseif (isset($city['county']) && is_array($city['county']) && isset($city['county']['id'])) {
            $currentCountyId = $city['county']['id'];
        }
    @endphp

    <form action="{{ route('cities.update', $city['id']) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- VÁROS NEVE --}}
        <div class="mb-3">
            <label for="name" class="form-label">Város neve:</label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="{{ old('name', $city['name']) }}" required>
        </div>

        {{-- MEGYE VÁLASZTÓ (FONTOS: Ez SELECT legyen!) --}}
        <div class="mb-3">
            <label for="county_id" class="form-label">Megye:</label>
            
            {{-- Itt a lényeg: SELECT, nem INPUT --}}
            <select name="county_id" id="county_id" class="form-select" required>
                <option value="">Válassz megyét...</option>
                @foreach($counties as $county)
                    <option value="{{ $county['id'] }}" 
                        {{ (old('county_id', $currentCountyId) == $county['id']) ? 'selected' : '' }}>
                        {{ $county['name'] }}
                    </option>
                @endforeach
            </select>
            
            <small class="text-muted">Jelenleg kiválasztott ID: {{ $currentCountyId ?? 'Nincs' }}</small>
        </div>

        <button type="submit" class="btn btn-primary">Mentés</button>
        <a href="{{ route('cities.index') }}" class="btn btn-secondary">Mégse</a>
    </form>
</div>
@endsection