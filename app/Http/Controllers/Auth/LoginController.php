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
        // 1. Megpróbálunk belépni az API-ba
        $response = $this->apiService->login($request->email, $request->password);

        // 2. Ha az API válasza hibás vagy nincs benne token
        if (!$response || !isset($response['token'])) {
            // Kiléptetjük a helyi user-t is, mert az API nélkül nem ér semmit
            Auth::logout();
            
            return redirect()->route('login')
                ->with('error', 'Helytelen bejelentkezési adatok.');
        }

        // 3. Siker! Elmentjük a tokent a munkamenetbe
        session(['api_token' => $response['token']]);

        // 4. Továbbítjuk a főoldalra
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        // Logout from API
        $this->apiService->logout();

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}