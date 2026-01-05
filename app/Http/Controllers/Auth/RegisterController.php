<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ZipApiService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';
    protected ZipApiService $apiService;

    public function __construct(ZipApiService $apiService)
    {
        $this->middleware('guest');
        $this->apiService = $apiService;
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], 
        ]);
    }

    protected function registered(Request $request, $user)
    {
        $apiData = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ];

        Log::info('API regisztráció indítása: ' . $user->email);

        $apiRegisterResponse = $this->apiService->register($apiData);

        if (!$apiRegisterResponse) {
            $user->delete();
            
            Log::error('API regisztráció sikertelen, felhasználó visszavonva.');
            
            $this->guard()->logout();

            return redirect()->back()
                ->withInput()
                ->withErrors(['email' => 'Hiba történt a központi rendszer elérésekor. Kérlek ellenőrizd a kapcsolatot vagy próbáld később.']);
        }

        $loginResponse = $this->apiService->login($user->email, $request->password);

        if ($loginResponse && isset($loginResponse['token'])) {
             session(['api_token' => $loginResponse['token']]);
        }
    }
}