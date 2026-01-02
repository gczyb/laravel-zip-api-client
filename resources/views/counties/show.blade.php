@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Megye részletei</h4>
                    @auth
                        <div>
                            <a href="{{ route('counties.edit', $county['id']) }}" class="btn btn-warning btn-sm">
                                Szerkesztés
                            </a>
                        </div>
                    @endauth
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">ID</th>
                            <td>{{ $county['id'] }}</td>
                        </tr>
                        <tr>
                            <th>Megye neve</th>
                            <td><strong>{{ $county['name'] }}</strong></td>
                        </tr>
                        <tr>
                            <th>Létrehozva</th>
                            <td>{{ \Carbon\Carbon::parse($county['created_at'])->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Módosítva</th>
                            <td>{{ \Carbon\Carbon::parse($county['updated_at'])->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('counties.index') }}" class="btn btn-secondary">
                            Vissza a listához
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection