<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        $login = request()->input('username');
        
        if(filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
        
        return 'username';
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    }
    
    protected function authenticated(Request $request, $user)
    {
        // Regenerate session to prevent fixation
        $request->session()->regenerate();
        
        // Redirect based on role
        if ($user->isRegularUser()) {
            return redirect()->route('user.dashboard');
        }
        
        // Admin and Manager go to admin dashboard
        return redirect()->route('admin.dashboard');
    }
}