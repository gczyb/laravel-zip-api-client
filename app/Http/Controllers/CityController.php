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

    public function index()
    {
        $response = $this->apiService->getCities();
        
        $cities = [];
        if (is_array($response)) {
            $cities = isset($response['data']) ? $response['data'] : $response;
        }

        $countiesResponse = $this->apiService->getCounties();
        $counties = [];
        if (is_array($countiesResponse)) {
            $counties = isset($countiesResponse['data']) ? $countiesResponse['data'] : $countiesResponse;
        }

        return view('cities.index', compact('cities', 'counties'));
    }

    public function create()
    {
        $countiesResponse = $this->apiService->getCounties();
        $counties = [];
        if (is_array($countiesResponse)) {
            $counties = isset($countiesResponse['data']) ? $countiesResponse['data'] : $countiesResponse;
        }

        $postalCodesResponse = $this->apiService->getPostalCodes();
        $postalCodes = [];
        if (is_array($postalCodesResponse)) {
            $postalCodes = isset($postalCodesResponse['data']) ? $postalCodesResponse['data'] : $postalCodesResponse;
        }

        return view('cities.create', compact('counties', 'postalCodes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'county_id' => 'required|integer',
            'postal_code' => 'required|string|size:4',
        ]);

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

    public function show($id)
    {
        $response = $this->apiService->getCities(); 
        $allCities = isset($response['data']) ? $response['data'] : $response;

        $city = collect($allCities)->first(function ($item) use ($id) {
            return isset($item['id']) && $item['id'] == $id;
        });

        if (!$city) {
            abort(404);
        }

        return view('cities.show', compact('city'));
    }

    public function edit($id)
    {
        $response = $this->apiService->getCities();
        $allCities = isset($response['data']) ? $response['data'] : $response;

        $city = collect($allCities)->first(function ($item) use ($id) {
            return isset($item['id']) && $item['id'] == $id;
        });

        if (!$city) {
            abort(404);
        }

        $countiesResponse = $this->apiService->getCounties();
        $counties = isset($countiesResponse['data']) ? $countiesResponse['data'] : $countiesResponse;

        return view('cities.edit', compact('city', 'counties'));
    }

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

    public function destroy($id)
    {
        $response = $this->apiService->deleteCity($id);

        if ($response) {
            return redirect()->route('cities.index')
                ->with('success', 'Város sikeresen törölve!');
        }

        return back()->with('error', 'Hiba történt a város törlése során.');
    }

    public function filter(Request $request)
    {
        $countyId = $request->input('county_id');
        $letter = $request->input('letter');

        if ($countyId && !$letter) {
            $response = $this->apiService->getCityFirstLetters($countyId);
            
            
            $letters = [];
            if (is_array($response)) {
                if (isset($response['data'])) {
                    $letters = $response['data'];
                } else {
                    $letters = $response;
                }
            }
            
            return response()->json(['letters' => $letters]);
        }

        if ($countyId && $letter) {
            $response = $this->apiService->getCitiesByLetter($countyId, $letter);
            
            $cities = [];
            if (is_array($response)) {
                if (isset($response['data'])) {
                    $cities = $response['data'];
                } else {
                    $cities = $response;
                }
            }
            
            return response()->json(['cities' => $cities]);
        }

        return response()->json(['error' => 'Invalid parameters'], 400);
    }

    public function exportCsv(Request $request)
    {
        $countyId = $request->input('county_id');
        $letter = $request->input('letter');

        if ($countyId && $letter) {
            $response = $this->apiService->getCitiesByLetter($countyId, $letter);
            $cities = isset($response['data']) ? $response['data'] : $response;
        } else {
            $response = $this->apiService->getCities();
            $cities = isset($response['data']) ? $response['data'] : $response;
        }

        $filename = 'cities_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($cities) {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Város', 'Megye', 'Irányítószám']);
            
            foreach ($cities as $city) {
                $postalCode = isset($city['postal_codes'][0]['code']) ? $city['postal_codes'][0]['code'] : 'N/A';
                $countyName = isset($city['county']['name']) ? $city['county']['name'] : 'N/A';
                
                fputcsv($file, [
                    $city['name'],
                    $countyName,
                    $postalCode
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $countyId = $request->input('county_id');
        $letter = $request->input('letter');

        if ($countyId && $letter) {
            $response = $this->apiService->getCitiesByLetter($countyId, $letter);
            $cities = isset($response['data']) ? $response['data'] : $response;
            
            $countiesResponse = $this->apiService->getCounties();
            $allCounties = isset($countiesResponse['data']) ? $countiesResponse['data'] : $countiesResponse;
            
            $countyName = 'Ismeretlen';
            if (is_array($allCounties)) {
                foreach ($allCounties as $c) {
                    if (isset($c['id']) && $c['id'] == $countyId) {
                        $countyName = $c['name'];
                        break;
                    }
                }
            }

            $title = "Városok - {$countyName} megye - {$letter} betű";
        } 
        else 
        {
            $response = $this->apiService->getCities();
            $cities = isset($response['data']) ? $response['data'] : $response;
            $title = "Összes város";
        }

        $pdf = Pdf::loadView('cities.pdf', 
        [
            'cities' => $cities,
            'title' => $title,
            'date' => date('Y-m-d H:i:s')
        ]);
        
        return $pdf->download('cities_' . date('Y-m-d_His') . '.pdf');
    }
}