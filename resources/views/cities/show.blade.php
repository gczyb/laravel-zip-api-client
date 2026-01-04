@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Város részletei</h4>
                    @auth
                        <div>
                            <a href="{{ route('cities.edit', $city['id']) }}" class="btn btn-warning btn-sm">
                                Szerkesztés
                            </a>
                        </div>
                    @endauth
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                    <tr>
                        <th width="200">ID</th>
                        <td>{{ $city['id'] ?? $city->id ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Város neve</th>
                        <td><strong>{{ $city['name'] ?? $city->name ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <th>Megye</th>
                        <td>
                            @php
                                $county = $city['county'] ?? $city->county ?? null;
                                $countyId = is_array($county) ? ($county['id'] ?? null) : ($county->id ?? null);
                                $countyName = is_array($county) ? ($county['name'] ?? null) : ($county->name ?? null);
                            @endphp
                            
                            @if($countyId && $countyName)
                                <a href="{{ route('counties.show', $countyId) }}">
                                    {{ $countyName }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Irányítószám(ok)</th>
                        <td>
                            @php
                                $postalCodes = $city['postal_codes'] ?? $city->postal_codes ?? $city['postalCodes'] ?? $city->postalCodes ?? [];
                            @endphp
                            
                            @if(is_array($postalCodes) || is_object($postalCodes))
                                @forelse($postalCodes as $postalCode)
                                    @php
                                        $code = is_array($postalCode) ? ($postalCode['code'] ?? 'N/A') : ($postalCode->code ?? 'N/A');
                                    @endphp
                                    <span class="badge bg-secondary me-1">{{ $code }}</span>
                                @empty
                                    <span class="text-muted">Nincs irányítószám</span>
                                @endforelse
                            @else
                                <span class="text-muted">Nincs irányítószám</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Létrehozva</th>
                        <td>
                            @php
                                $createdAt = $city['created_at'] ?? $city->created_at ?? null;
                            @endphp
                            {{ $createdAt ? \Carbon\Carbon::parse($createdAt)->format('Y-m-d H:i:s') : 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <th>Módosítva</th>
                        <td>
                            @php
                                $updatedAt = $city['updated_at'] ?? $city->updated_at ?? null;
                            @endphp
                            {{ $updatedAt ? \Carbon\Carbon::parse($updatedAt)->format('Y-m-d H:i:s') : 'N/A' }}
                        </td>
                    </tr>
                </table>

                    <div class="mt-3">
                        <a href="{{ route('cities.index') }}" class="btn btn-secondary">
                            Vissza a listához
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection