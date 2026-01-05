<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ZipApiService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';
    protected ZipApiService $apiService;

    public function __construct(ZipApiService $apiService)
    {
        $this->apiService = $apiService;
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     */
    protected function authenticated(Request $request, $user)
    {
        $response = $this->apiService->login($request->email, $request->password);
        
        if (!$response || !isset($response['token'])) {
             dd('API Hiba történt:', $response); 
        }

        if (!$response || !isset($response['token'])) {
            Auth::logout();
            
            return redirect()->route('login')
                ->with('error', 'Helytelen bejelentkezési adatok.');
        }

        session(['api_token' => $response['token']]);

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        $this->apiService->logout();

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}