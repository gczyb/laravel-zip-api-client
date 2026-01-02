<?php

namespace App\Http\Controllers;

use App\Services\ZipApiService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CityController extends Controller
{
    protected ZipApiService $apiService;

    public function __construct(ZipApiService $apiService)
    {
        $this->apiService = $apiService;
        $this->middleware('auth')->except(['index', 'show', 'filter', 'exportCsv', 'exportPdf']);
    }

    /**
     * Display a listing of cities
     */
    public function index()
    {
        $response = $this->apiService->getCities();
        $cities = $response['data'] ?? [];

        $countiesResponse = $this->apiService->getCounties();
        $counties = $countiesResponse['data'] ?? [];

        return view('cities.index', compact('cities', 'counties'));
    }

    /**
     * Show the form for creating a new city
     */
    public function create()
    {
        $countiesResponse = $this->apiService->getCounties();
        $counties = $countiesResponse['data'] ?? [];

        $postalCodesResponse = $this->apiService->getPostalCodes();
        $postalCodes = $postalCodesResponse['data'] ?? [];

        return view('cities.create', compact('counties', 'postalCodes'));
    }

    /**
     * Store a newly created city
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'county_id' => 'required|integer',
            'postal_code' => 'required|string|size:4',
        ]);

        // First, create or get postal code
        $postalCodeData = [
            'code' => $request->postal_code,
            'city_id' => null // Will be set after city creation
        ];

        // Create city
        $cityData = [
            'name' => $request->name,
            'county_id' => $request->county_id,
        ];

        $response = $this->apiService->createCity($cityData);

        if ($response && isset($response['data'])) {
            return redirect()->route('cities.index')
                ->with('success', 'Város sikeresen létrehozva!');
        }

        return back()->with('error', 'Hiba történt a város létrehozása során.')->withInput();
    }

    /**
     * Display the specified city
     */
    public function show($id)
    {
        $response = $this->apiService->getCity($id);
        $city = $response['data'] ?? null;

        if (!$city) {
            abort(404);
        }

        return view('cities.show', compact('city'));
    }

    /**
     * Show the form for editing the specified city
     */
    public function edit($id)
    {
        $response = $this->apiService->getCity($id);
        $city = $response['data'] ?? null;

        if (!$city) {
            abort(404);
        }

        $countiesResponse = $this->apiService->getCounties();
        $counties = $countiesResponse['data'] ?? [];

        return view('cities.edit', compact('city', 'counties'));
    }

    /**
     * Update the specified city
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'county_id' => 'required|integer',
        ]);

        $response = $this->apiService->updateCity($id, $request->only(['name', 'county_id']));

        if ($response) {
            return redirect()->route('cities.index')
                ->with('success', 'Város sikeresen frissítve!');
        }

        return back()->with('error', 'Hiba történt a város frissítése során.');
    }

    /**
     * Remove the specified city
     */
    public function destroy($id)
    {
        $response = $this->apiService->deleteCity($id);

        if ($response) {
            return redirect()->route('cities.index')
                ->with('success', 'Város sikeresen törölve!');
        }

        return back()->with('error', 'Hiba történt a város törlése során.');
    }

    /**
     * Filter cities by county and letter
     */
    public function filter(Request $request)
    {
        $countyId = $request->input('county_id');
        $letter = $request->input('letter');

        if ($countyId && !$letter) {
            // Get first letters for selected county
            $response = $this->apiService->getCityFirstLetters($countyId);
            return response()->json(['letters' => $response['data'] ?? []]);
        }

        if ($countyId && $letter) {
            // Get cities by county and letter
            $response = $this->apiService->getCitiesByLetter($countyId, $letter);
            return response()->json(['cities' => $response['data'] ?? []]);
        }

        return response()->json(['error' => 'Invalid parameters'], 400);
    }

    /**
     * Export cities to CSV
     */
    public function exportCsv(Request $request)
    {
        $countyId = $request->input('county_id');
        $letter = $request->input('letter');

        if ($countyId && $letter) {
            $response = $this->apiService->getCitiesByLetter($countyId, $letter);
            $cities = $response['data'] ?? [];
        } else {
            $response = $this->apiService->getCities();
            $cities = $response['data'] ?? [];
        }

        $filename = 'cities_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($cities) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, ['Város', 'Megye', 'Irányítószám']);
            
            // Data rows
            foreach ($cities as $city) {
                $postalCode = $city['postal_codes'][0]['code'] ?? 'N/A';
                fputcsv($file, [
                    $city['name'],
                    $city['county']['name'] ?? 'N/A',
                    $postalCode
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export cities to PDF
     */
    public function exportPdf(Request $request)
    {
        $countyId = $request->input('county_id');
        $letter = $request->input('letter');

        if ($countyId && $letter) {
            $response = $this->apiService->getCitiesByLetter($countyId, $letter);
            $cities = $response['data'] ?? [];
            
            $countyResponse = $this->apiService->getCounty($countyId);
            $countyName = $countyResponse['data']['name'] ?? 'Ismeretlen';
            $title = "Városok - {$countyName} megye - {$letter} betű";
        } else {
            $response = $this->apiService->getCities();
            $cities = $response['data'] ?? [];
            $title = "Összes város";
        }

        $pdf = Pdf::loadView('cities.pdf', [
            'cities' => $cities,
            'title' => $title,
            'date' => date('Y-m-d H:i:s')
        ]);

        return $pdf->download('cities_' . date('Y-m-d_His') . '.pdf');
    }
}