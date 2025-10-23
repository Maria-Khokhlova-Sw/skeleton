<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $user = User::where('login', $request->login)->first();

        if ($user && $user->is_blocked) {
            return redirect('/login')->with('error', 'Пользователь заблокирован, обратитесь к администратору');
        }

        if ($user && $user->password === $request->password) {
            Auth::login($user);
            $user->number_attempt = 0;
            $user->save();
            return redirect('/dashboard');
        }

        if ($user && $user->password !== $request->password) {
            $user->number_attempt = $user->number_attempt + 1;
            $user->save();
            if ($user->number_attempt >= 3) {
                $user->is_blocked = true;
                $user->save();
            }
            return redirect('/login')->with('error', 'Неверный пароль');
        }

        return redirect('/login')->with('error', 'Неверный пароль');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}