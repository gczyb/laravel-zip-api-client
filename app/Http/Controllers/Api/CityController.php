<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use Illuminate\Http\Request;

class CityController extends Controller
{

    public function index()
    {
        $cities = City::with(['county', 'postalCodes'])->get();
        return CityResource::collection($cities);
    }

    public function store(StoreCityRequest $request)
    {
        $city = City::create($request->validated());
        return new CityResource($city->load(['county', 'postalCodes']));
    }

    public function show(City $city)
    {
        return new CityResource($city->load(['county', 'postalCodes']));
    }

    public function update(UpdateCityRequest $request, City $city)
    {
        $city->update($request->validated());
        return new CityResource($city->load(['county', 'postalCodes']));
    }

    public function destroy(City $city)
    {
        $city->delete();
        return response()->json(['message' => 'City deleted successfully']);
    }

    public function getFirstLetters($countyId)
    {
        $letters = City::where('county_id', $countyId)
            ->selectRaw('DISTINCT UPPER(SUBSTRING(name, 1, 1)) as letter')
            ->orderBy('letter')
            ->pluck('letter');

        return response()->json(['data' => $letters]);
    }

    public function getCitiesByLetter($countyId, $letter)
    {
        $cities = City::with('postalCodes', 'county')
            ->where('county_id', $countyId)
            ->whereRaw('UPPER(SUBSTRING(name, 1, 1)) = ?', [strtoupper($letter)])
            ->orderBy('name')
            ->get();

        return CityResource::collection($cities);
    }
}