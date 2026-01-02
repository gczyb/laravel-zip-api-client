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
                            <td>{{ $city['id'] }}</td>
                        </tr>
                        <tr>
                            <th>Város neve</th>
                            <td><strong>{{ $city['name'] }}</strong></td>
                        </tr>
                        <tr>
                            <th>Megye</th>
                            <td>
                                @if(isset($city['county']))
                                    <a href="{{ route('counties.show', $city['county']['id']) }}">
                                        {{ $city['county']['name'] }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Irányítószám(ok)</th>
                            <td>
                                @if(isset($city['postal_codes']) && count($city['postal_codes']) > 0)
                                    @foreach($city['postal_codes'] as $postalCode)
                                        <span class="badge bg-secondary me-1">{{ $postalCode['code'] }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Nincs irányítószám</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Létrehozva</th>
                            <td>{{ \Carbon\Carbon::parse($city['created_at'])->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Módosítva</th>
                            <td>{{ \Carbon\Carbon::parse($city['updated_at'])->format('Y-m-d H:i:s') }}</td>
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