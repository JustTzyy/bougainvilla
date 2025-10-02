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
    use EnhancedLoggingTrait;

    public function loginInterface()
    {
        return view('auth.login'); 
    }

    public function login(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);

            $credentials = $request->only('email', 'password');

            // Check if user exists and account is not soft deleted
            $user = User::where(function($query) use ($credentials) {
                $query->where('email', $credentials['email'])
                      ->orWhere('name', $credentials['email']);
            })->whereNull('deleted_at')->first();

            if (!$user) {
                $this->logSecurityEvent('Login attempt with non-existent user', [
                    'email' => $credentials['email']
                ]);
                return redirect()->route('login')
                    ->withErrors(['email' => 'Invalid username/email or password.'])
                    ->withInput($request->only('email'));
            }

            if (!Hash::check($credentials['password'], $user->password)) {
                $this->logSecurityEvent('Login attempt with invalid password', [
                    'user_id' => $user->id,
                    'email' => $credentials['email']
                ]);
                return redirect()->route('login')
                    ->withErrors(['email' => 'Invalid username/email or password.'])
                    ->withInput($request->only('email'));
            }

            // Check if user status is active
            if (isset($user->status) && $user->status !== 'Active') {
                \Log::warning('Login attempt with inactive account', [
                    'user_id' => $user->id,
                    'status' => $user->status,
                    'ip' => $request->ip()
                ]);
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account is currently inactive. Please contact an administrator.'])
                    ->withInput($request->only('email'));
            }

            // Successful authentication
            Auth::login($user);

            // Log successful login
            $this->logUserAction('Successful login', [
                'user_id' => $user->id,
                'role' => $user->roleID == 1 ? 'Admin' : 'Front Desk'
            ]);

            try {
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
            } catch (\Exception $e) {
                // Log the error but don't fail the login
                $this->logDatabaseOperation('Failed to create login history', 'History/Log', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ], 'error');
            }

            // Role-based redirection with validation
            if ($user->roleID == 1) {
                Cookie::queue('username', $user->name, 60);
                return redirect()->route('adminPages.dashboard');
            } elseif ($user->roleID == 2) {
                Cookie::queue('username', $user->name, 60);
                return redirect()->route('frontdesk.dashboard');
            } else {
                \Log::error('User with unrecognized role attempted login', [
                    'user_id' => $user->id,
                    'role_id' => $user->roleID
                ]);
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account role is not properly configured. Please contact an administrator.'])
                    ->withInput($request->only('email'));
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('login')
                ->withErrors($e->errors())
                ->withInput($request->only('email'));
        } catch (\Exception $e) {
            \Log::error('Login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip()
            ]);
            return redirect()->route('login')
                ->withErrors(['email' => 'An error occurred during login. Please try again.'])
                ->withInput($request->only('email'));
        }
    }

    public function logout(Request $request)
    {
        try {
            // Log logout activity before logging out
            if (Auth::check()) {
                $user = Auth::user();
                
                try {
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
                } catch (\Exception $e) {
                    // Log the error but don't fail the logout
                    \Log::error('Failed to create logout history', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login');
        } catch (\Exception $e) {
            \Log::error('Logout error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            // Force logout even if there's an error
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login');
        }
    }


}
