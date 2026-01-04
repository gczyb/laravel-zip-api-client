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
        // Ez olvassa ki a config/services.php-ból a beállított URL-t.
        // Fontos: a config/services.php-ban a 'zip_api' => 'base_url'
        // mutasson az .env API_BASE_URL változójára!
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

        // Itt fűzzük össze a Base URL-t (pl http://localhost:8001/api) az endpointtal
        $fullUrl = $this->baseUrl . $endpoint;
        
        // DEBUG LOGOLÁS (Hogy lásd a hibát a storage/logs/laravel.log-ban, ha van)
        Log::info('=== API REQUEST START ===');
        Log::info('Full URL: ' . $fullUrl);

        try {
            $response = Http::withHeaders($headers)
                ->$method($fullUrl, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('API Request Failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('=== API REQUEST EXCEPTION ===');
            Log::error($e->getMessage());
            return null;
        }
    }

    // --- JAVÍTOTT METÓDUSOK (Kivettük a /auth részt) ---

    public function register(array $data)
    {
        // JAVÍTVA: /auth/register helyett /register
        // Így a végleges URL ez lesz: .../api/register
        return $this->request('post', '/register', $data);
    }

    public function login(string $email, string $password)
    {
        // JAVÍTVA: /auth/login helyett /login
        $response = $this->request('post', '/login', [
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
        // JAVÍTVA: /auth/logout helyett /logout
        $response = $this->request('post', '/logout');
        $this->setToken(null);
        return $response;
    }

    // --- EGYÉB METÓDUSOK (Ezek változatlanok, de itt vannak a teljesség kedvéért) ---

    public function getCounties()
    {
        return $this->request('get', '/counties');
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