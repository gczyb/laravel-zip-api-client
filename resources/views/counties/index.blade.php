@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Megyék listája</h4>
                    @auth
                        <a href="{{ route('counties.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Új megye hozzáadása
                        </a>
                    @endauth
                </div>

                <div class="card-body">
                    @if(empty($counties))
                        <div class="alert alert-info">
                            Nincsenek megyék az adatbázisban.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Megye neve</th>
                                        <th>Létrehozva</th>
                                        @auth
                                            <th class="text-end">Műveletek</th>
                                        @endauth
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($counties as $county)
                                        <tr>
                                            <td>{{ $county['id'] }}</td>
                                            <td>
                                                <strong>{{ $county['name'] }}</strong>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($county['created_at'])->format('Y-m-d') }}</td>
                                            @auth
                                                <td class="text-end">
                                                    <div class="action-buttons justify-content-end">
                                                        <a href="{{ route('counties.show', $county['id']) }}" 
                                                           class="btn btn-sm btn-info">
                                                            Megtekintés
                                                        </a>
                                                        <a href="{{ route('counties.edit', $county['id']) }}" 
                                                           class="btn btn-sm btn-warning">
                                                            Szerkesztés
                                                        </a>
                                                        <form action="{{ route('counties.destroy', $county['id']) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Biztosan törölni szeretnéd ezt a megyét?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                Törlés
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endauth
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection