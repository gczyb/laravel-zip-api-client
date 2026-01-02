@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Városok ABC szerinti szűrése</h4>
                    @auth
                        <a href="{{ route('cities.create') }}" class="btn btn-primary">
                            Új város hozzáadása
                        </a>
                    @endauth
                </div>

                <div class="card-body">
                    <!-- Megye választó -->
                    <div class="mb-4">
                        <label for="countySelect" class="form-label fw-bold">1. Válassz megyét:</label>
                        <select id="countySelect" class="form-select">
                            <option value="">-- Válassz megyét --</option>
                            @foreach($counties as $county)
                                <option value="{{ $county['id'] }}">{{ $county['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kezdőbetű szűrő -->
                    <div id="letterFilterContainer" class="mb-4" style="display: none;">
                        <label class="form-label fw-bold">2. Válassz kezdőbetűt:</label>
                        <div id="letterButtons" class="d-flex flex-wrap gap-2"></div>
                    </div>

                    <!-- Exportálás gombok -->
                    <div id="exportButtons" class="mb-4" style="display: none;">
                        <label class="form-label fw-bold">3. Exportálás:</label>
                        <div class="d-flex gap-2">
                            <button id="exportCsvBtn" class="btn btn-success">
                                📊 CSV Export
                            </button>
                            <button id="exportPdfBtn" class="btn btn-danger">
                                📄 PDF Export
                            </button>
                        </div>
                    </div>

                    <!-- Eredmények táblázata -->
                    <div id="resultsContainer" style="display: none;">
                        <h5 class="mb-3">Találatok:</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="citiesTable">
                                <thead>
                                    <tr>
                                        <th>Város neve</th>
                                        <th>Megye</th>
                                        <th>Irányítószám</th>
                                        @auth
                                            <th class="text-end">Műveletek</th>
                                        @endauth
                                    </tr>
                                </thead>
                                <tbody id="citiesTableBody">
                                    <!-- AJAX töltés ide -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Betöltés jelző -->
                    <div id="loadingSpinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Betöltés...</span>
                        </div>
                        <p class="mt-2">Adatok betöltése...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedCountyId = null;
let selectedLetter = null;

$(document).ready(function() {
    // Megye kiválasztása
    $('#countySelect').on('change', function() {
        selectedCountyId = $(this).val();
        selectedLetter = null;
        
        $('#letterFilterContainer').hide();
        $('#letterButtons').empty();
        $('#resultsContainer').hide();
        $('#exportButtons').hide();
        $('#citiesTableBody').empty();
        
        if (selectedCountyId) {
            loadFirstLetters(selectedCountyId);
        }
    });
    
    // Kezdőbetűk betöltése
    function loadFirstLetters(countyId) {
        $('#loadingSpinner').show();
        
        $.ajax({
            url: '{{ route("cities.filter") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                county_id: countyId
            },
            success: function(response) {
                $('#loadingSpinner').hide();
                
                if (response.letters && response.letters.length > 0) {
                    $('#letterButtons').empty();
                    
                    response.letters.forEach(function(letter) {
                        let btn = $('<button>')
                            .addClass('btn btn-outline-primary letter-btn')
                            .text(letter)
                            .attr('data-letter', letter);
                        
                        $('#letterButtons').append(btn);
                    });
                    
                    $('#letterFilterContainer').show();
                    
                    // Betű kattintás esemény
                    $('.letter-btn').on('click', function() {
                        $('.letter-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
                        $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
                        
                        selectedLetter = $(this).data('letter');
                        loadCitiesByLetter(selectedCountyId, selectedLetter);
                    });
                } else {
                    alert('Ebben a megyében nincsenek városok.');
                }
            },
            error: function() {
                $('#loadingSpinner').hide();
                alert('Hiba történt a betűk betöltése során.');
            }
        });
    }
    
    // Városok betöltése betű szerint
    function loadCitiesByLetter(countyId, letter) {
        $('#loadingSpinner').show();
        $('#resultsContainer').hide();
        
        $.ajax({
            url: '{{ route("cities.filter") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                county_id: countyId,
                letter: letter
            },
            success: function(response) {
                $('#loadingSpinner').hide();
                
                if (response.cities && response.cities.length > 0) {
                    $('#citiesTableBody').empty();
                    
                    response.cities.forEach(function(city) {
                        let postalCode = city.postal_codes && city.postal_codes.length > 0 
                            ? city.postal_codes[0].code 
                            : 'N/A';
                        
                        let row = `
                            <tr>
                                <td><strong>${city.name}</strong></td>
                                <td>${city.county ? city.county.name : 'N/A'}</td>
                                <td><span class="badge bg-secondary">${postalCode}</span></td>
                                @auth
                                <td class="text-end">
                                    <a href="/cities/${city.id}" class="btn btn-sm btn-info">Megtekintés</a>
                                    <a href="/cities/${city.id}/edit" class="btn btn-sm btn-warning">Szerkesztés</a>
                                </td>
                                @endauth
                            </tr>
                        `;
                        
                        $('#citiesTableBody').append(row);
                    });
                    
                    $('#resultsContainer').show();
                    $('#exportButtons').show();
                } else {
                    alert('Nincsenek városok erre a betűre.');
                }
            },
            error: function() {
                $('#loadingSpinner').hide();
                alert('Hiba történt a városok betöltése során.');
            }
        });
    }
    
    // CSV export
    $('#exportCsvBtn').on('click', function() {
        if (selectedCountyId && selectedLetter) {
            window.location.href = `{{ route('cities.export.csv') }}?county_id=${selectedCountyId}&letter=${selectedLetter}`;
        }
    });
    
    // PDF export
    $('#exportPdfBtn').on('click', function() {
        if (selectedCountyId && selectedLetter) {
            window.location.href = `{{ route('cities.export.pdf') }}?county_id=${selectedCountyId}&letter=${selectedLetter}`;
        }
    });
});
</script>
@endpush