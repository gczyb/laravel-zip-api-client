@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h4>Üdvözöllek, {{ Auth::user()->name }}!</h4>
                    <p class="text-muted">Sikeresen bejelentkeztél az alkalmazásba.</p>

                    <hr>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Megyék kezelése</h5>
                                    <p class="card-text">Böngészd, szerkeszd és kezelj megyéket.</p>
                                    <a href="{{ route('counties.index') }}" class="btn btn-light">Megyék megtekintése</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Városok kezelése</h5>
                                    <p class="card-text">Keress, szűrj és exportálj városokat.</p>
                                    <a href="{{ route('cities.index') }}" class="btn btn-light">Városok megtekintése</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection