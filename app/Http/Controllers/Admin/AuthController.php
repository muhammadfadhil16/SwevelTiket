<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use \App\Notifications\LoginNotification;
use \App\Notifications\RegistrationNotification;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function showRegisterForm()
    {
        return view('admin.auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email_user' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email_user', $request->input('email_user'))
            ->orWhere('name_user', $request->input('email_user'))
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $user->notify(new LoginNotification($user->name_user, now()->toDateTimeString()));

            if ($user->role === 'Admin') {
                return redirect()->route('admin.dashboard')
                    ->with(['status' => 'success', 'message' => 'Berhasil login sebagai Admin!']);
            }
            if ($user->role === 'User') {
                return redirect()->route('catalogue.index')
                    ->with(['status' => 'success', 'message' => 'Selamat datang, ' . $user->name_user ]);
            }
            return redirect()->route('catalogue.index')
                ->with(['status' => 'success', 'message' => 'Selamat datang, ' . $user->name_user .'!']);
        }

        // Jika gagal
        return back()->with(['status' => 'error', 'message' => 'Email atau password salah']);
    }

    public function redirectUserByRole($route)
    {
        if (Auth::user()->role === 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::user()->role === 'User') {
            return redirect()->route('catalogue.index');
        }

        return redirect()->route($route);
    }

    public function register(Request $request)
    {

        $request->validate([
            'name_user' => 'required|string|max:255|unique:users,name_user',
            'email_user' => 'required|string|email|max:255|unique:users,email_user',
            'password' => 'required|string|min:8|confirmed',
        ]);


        $user = User::create([
            'name_user' => $request->name_user,
            'email_user' => $request->email_user,
            'password' => Hash::make($request->password),
            // Public registration must always create regular users.
            'role' => 'User',
        ]);

        $user->notify(new RegistrationNotification($user->name_user));

        Auth::login($user);


        return $this->redirectUserByRole('catalogue.index')->with([
            'status' => 'success',
            'message' => 'Berhasil mendaftar! Selamat datang, ' . $user->name_user
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('catalogue.index')->with([
            'status' => 'error',
            'message' => 'Berhasil logout!'
        ]);
    }
}
