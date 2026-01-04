<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZipApiService
{
    protected string $baseUrl;
    protected ?string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.zip_api.base_url');
        $this->token = session('api_token');
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
        session(['api_token' => $token]);
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    protected function request(string $method, string $endpoint, array $data = [])
    {
        $headers = [];
        
        if ($this->token) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        $fullUrl = $this->baseUrl . $endpoint;
        
        // DEBUG INFO
        \Log::info('=== API REQUEST START ===');
        \Log::info('Method: ' . $method);
        \Log::info('Base URL: ' . $this->baseUrl);
        \Log::info('Endpoint: ' . $endpoint);
        \Log::info('Full URL: ' . $fullUrl);
        \Log::info('Headers: ', $headers);
        \Log::info('Data: ', $data);

        try {
            $response = Http::withHeaders($headers)
                ->$method($fullUrl, $data);

            \Log::info('Response Status: ' . $response->status());
            \Log::info('Response Body: ' . $response->body());

            if ($response->successful()) {
                \Log::info('=== API REQUEST SUCCESS ===');
                return $response->json();
            }

            \Log::error('API Request Failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            \Log::error('=== API REQUEST EXCEPTION ===');
            \Log::error('Exception: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    public function register(array $data)
    {
        return $this->request('post', '/auth/register', $data);
    }

    public function login(string $email, string $password)
    {
        $response = $this->request('post', '/auth/login', [
            'email' => $email,
            'password' => $password
        ]);

        if ($response && isset($response['token'])) {
            $this->setToken($response['token']);
        }

        return $response;
    }

    public function logout()
    {
        $response = $this->request('post', '/auth/logout');
        $this->setToken(null);
        return $response;
    }

    public function getCounties()
    {
        $endpoint = '/counties';
        $fullUrl = $this->baseUrl . $endpoint;     
        $response = $this->request('get', $endpoint);
        
        return $response;
    }

    public function getCounty(int $id)
    {
        return $this->request('get', "/counties/{$id}");
    }

    public function createCounty(array $data)
    {
        return $this->request('post', '/counties', $data);
    }

    public function updateCounty(int $id, array $data)
    {
        return $this->request('put', "/counties/{$id}", $data);
    }

    public function deleteCounty(int $id)
    {
        return $this->request('delete', "/counties/{$id}");
    }

    public function getCities(array $filters = [])
    {
        $query = http_build_query($filters);
        $endpoint = '/cities' . ($query ? '?' . $query : '');
        return $this->request('get', $endpoint);
    }

    public function getCity(int $id)
    {
        return $this->request('get', "/cities/{$id}");
    }

    public function createCity(array $data)
    {
        return $this->request('post', '/cities', $data);
    }

    public function updateCity(int $id, array $data)
    {
        return $this->request('put', "/cities/{$id}", $data);
    }

    public function deleteCity(int $id)
    {
        return $this->request('delete', "/cities/{$id}");
    }

    public function getCityFirstLetters(int $countyId)
    {
        return $this->request('get', "/cities/first-letters/{$countyId}");
    }

    public function getCitiesByLetter(int $countyId, string $letter)
    {
        return $this->request('get', "/cities/by-letter/{$countyId}/{$letter}");
    }

    public function getPostalCodes()
    {
        return $this->request('get', '/postal-codes');
    }

    public function getPostalCode(int $id)
    {
        return $this->request('get', "/postal-codes/{$id}");
    }

    public function createPostalCode(array $data)
    {
        return $this->request('post', '/postal-codes', $data);
    }

    public function updatePostalCode(int $id, array $data)
    {
        return $this->request('put', "/postal-codes/{$id}", $data);
    }

    public function deletePostalCode(int $id)
    {
        return $this->request('delete', "/postal-codes/{$id}");
    }

    public function search(array $params)
    {
        $query = http_build_query($params);
        return $this->request('get', '/search?' . $query);
    }
}