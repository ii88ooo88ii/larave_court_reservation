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
        if ($user->isRegularUser()) {
            return redirect()->route('user.dashboard');
        }
        return redirect()->route('admin.dashboard');
    }
}