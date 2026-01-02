<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ZipApiService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected ZipApiService $apiService;

    public function __construct(ZipApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Handle search requests and return API response as JSON.
     */
    public function search(Request $request)
    {
        // Accept any query parameters and forward them to the API service
        $params = $request->all();

        $response = $this->apiService->search($params);

        // If the service returns null or an unexpected shape, normalize to an empty result
        if (!is_array($response)) {
            return response()->json(['data' => []], 200);
        }

        return response()->json($response);
    }
}
