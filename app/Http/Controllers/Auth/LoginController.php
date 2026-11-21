<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if (!$user->status) {
            return redirect()->route('pending-approval');
        }
    }

    protected function redirectTo()
    {
        $user = auth()->user();

        if ($user && !$user->status) {
            return route('pending-approval');
        }

        return $user?->defaultAdminRoute() ?? '/dashboard';
    }

    /**
     * Keep Laravel UI's existing throttling behaviour and only add audit
     * logging. The response stays generic so account existence is not exposed.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        Log::warning('Failed admin login attempt', [
            'email' => $request->input($this->username()),
            'ip' => $request->ip(),
        ]);

        // Same generic response as Laravel UI; trait methods cannot be
        // reached through parent:: because traits are copied into this class.
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    protected function sendLockoutResponse(Request $request)
    {
        Log::warning('Admin login throttled', [
            'email' => $request->input($this->username()),
            'ip' => $request->ip(),
        ]);

        $seconds = $this->limiter()->availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ])->status(Response::HTTP_TOO_MANY_REQUESTS);
    }
}
