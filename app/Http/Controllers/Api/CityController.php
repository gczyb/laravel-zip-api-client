<?php
/**
 * Get first letters of cities in a county
 */
public function getFirstLetters($countyId)
{
    $letters = City::where('county_id', $countyId)
        ->selectRaw('DISTINCT UPPER(SUBSTRING(name, 1, 1)) as letter')
        ->orderBy('letter')
        ->pluck('letter');

    return response()->json(['data' => $letters]);
}

/**
 * Get cities by county and first letter
 */
public function getCitiesByLetter($countyId, $letter)
{
    $cities = City::with('postalCodes')
        ->where('county_id', $countyId)
        ->whereRaw('UPPER(SUBSTRING(name, 1, 1)) = ?', [strtoupper($letter)])
        ->orderBy('name')
        ->get();

    return CityResource::collection($cities);
}