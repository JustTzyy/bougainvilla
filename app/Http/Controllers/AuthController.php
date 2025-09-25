<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\History;
use App\Models\Log;
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

            // Log login activity to History table
            History::create([
                'userID' => $user->id,
                'status' => 'User logged in: ' . $user->name . ' (Role: ' . ($user->roleID == 1 ? 'Admin' : 'Front Desk') . ')',
            ]);

            // Log login activity to Logs table
            Log::create([
                'userID' => $user->id,
                'timeIn' => now(),
                'status' => 'User logged in: ' . $user->name . ' (Role: ' . ($user->roleID == 1 ? 'Admin' : 'Front Desk') . ')',
            ]);

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
        // Log logout activity before logging out
        if (Auth::check()) {
            $user = Auth::user();
            
            // Log to History table
            History::create([
                'userID' => $user->id,
                'status' => 'User logged out: ' . $user->name . ' (Role: ' . ($user->roleID == 1 ? 'Admin' : 'Front Desk') . ')',
            ]);

            // Log to Logs table
            Log::create([
                'userID' => $user->id,
                'timeOut' => now(),
                'status' => 'User logged out: ' . $user->name . ' (Role: ' . ($user->roleID == 1 ? 'Admin' : 'Front Desk') . ')',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }


}
