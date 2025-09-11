<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{

    public function loginInterface()
    {
        return view('auth.login'); 
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])
            ->orWhere('name', $credentials['email'])
            ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);

            if ($user->roleID == 1) {
                Cookie::queue('username', $user->name, 60);

                return redirect()->route('adminPages.dashboard');
            } elseif ($user->roleID == 2) {
                return redirect('/Dashboard/teacher');
            } else {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Role not recognized.'])
                    ->withInput($request->only('email'));
            }
        } else {
            return redirect()->route('login')
                ->withErrors(['email' => 'Invalid username/email or password.'])
                ->withInput($request->only('email'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }


}
