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

    public function index()
    {
        $response = $this->apiService->getCounties();
        
        if (is_array($response)) {
            $counties = isset($response['data']) ? $response['data'] : $response;
        } else {
            $counties = [];
        }

        return view('counties.index', compact('counties'));
    }

    public function create()
    {
        return view('counties.create');
    }

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

    public function show($id)
    {
        $response = $this->apiService->getCounties();

        $allCounties = isset($response['data']) ? $response['data'] : $response;

        $county = collect($allCounties)->first(function ($item) use ($id) {
            return isset($item['id']) && $item['id'] == $id;
        });

        if (!$county) {
            abort(404);
        }

        return view('counties.show', compact('county'));
    }

    public function edit($id)
    {
        $response = $this->apiService->getCounties();
        $allCounties = isset($response['data']) ? $response['data'] : $response;

        $county = collect($allCounties)->first(function ($item) use ($id) {
            return isset($item['id']) && $item['id'] == $id;
        });

        if (!$county) {
            abort(404);
        }

        return view('counties.edit', compact('county'));
    }

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