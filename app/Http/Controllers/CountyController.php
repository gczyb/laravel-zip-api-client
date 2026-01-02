<?php

namespace App\Http\Controllers;

use App\Services\ZipApiService;
use Illuminate\Http\Request;

class CountyController extends Controller
{
    protected ZipApiService $apiService;

    public function __construct(ZipApiService $apiService)
    {
        $this->apiService = $apiService;
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of counties
     */
    public function index()
    {
        $response = $this->apiService->getCounties();
        
        // Handle both formats: direct array or wrapped in 'data'
        if (is_array($response)) {
            // Check if it's wrapped in 'data' key
            $counties = isset($response['data']) ? $response['data'] : $response;
        } else {
            $counties = [];
        }

        return view('counties.index', compact('counties'));
    }

    /**
     * Show the form for creating a new county
     */
    public function create()
    {
        return view('counties.create');
    }

    /**
     * Store a newly created county
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $response = $this->apiService->createCounty($request->only('name'));

        if ($response) {
            return redirect()->route('counties.index')
                ->with('success', 'Megye sikeresen létrehozva!');
        }

        return back()->with('error', 'Hiba történt a megye létrehozása során.');
    }

    /**
     * Display the specified county
     */
    public function show($id)
    {
        $response = $this->apiService->getCounty($id);
        
        // Handle both wrapped and direct response
        $county = isset($response['data']) ? $response['data'] : $response;

        if (!$county) {
            abort(404);
        }

        return view('counties.show', compact('county'));
    }

    /**
     * Show the form for editing the specified county
     */
    public function edit($id)
    {
        $response = $this->apiService->getCounty($id);
        
        // Handle both wrapped and direct response
        $county = isset($response['data']) ? $response['data'] : $response;

        if (!$county) {
            abort(404);
        }

        return view('counties.edit', compact('county'));
    }

    /**
     * Update the specified county
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $response = $this->apiService->updateCounty($id, $request->only('name'));

        if ($response) {
            return redirect()->route('counties.index')
                ->with('success', 'Megye sikeresen frissítve!');
        }

        return back()->with('error', 'Hiba történt a megye frissítése során.');
    }

    /**
     * Remove the specified county
     */
    public function destroy($id)
    {
        $response = $this->apiService->deleteCounty($id);

        if ($response) {
            return redirect()->route('counties.index')
                ->with('success', 'Megye sikeresen törölve!');
        }

        return back()->with('error', 'Hiba történt a megye törlése során.');
    }
}