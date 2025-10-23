<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->id_role != 2) {
                return redirect('/dashboard')->with('error', 'Доступ запрещён');
            }
            return $next($request);
        });
    }

    public function index()
    {
        try {
            $users = User::all();
            return view('auth.admin', compact('users'));
        } catch (\Exception $e) {
            return view('auth.admin', ['users' => collect([])])->with('error', 'Ошибка загрузки пользователей');
        }
    }

    public function store(Request $request)
    {
        try {
            if (empty($request->login)) {
                return redirect()->route('admin')->with('error', 'Поле login обязательно для заполнения');
            }

            User::create([
                'login' => $request->login,
                'password' => $request->password,
                'id_role' => $request->id_role,
            ]);

            return redirect()->route('admin')->with('success', 'Пользователь добавлен');
        } catch (\Exception $e) {
            return redirect()->route('admin')->with('error', 'Ошибка при добавлении пользователя: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);
            $user->login = $request->login;
            $user->id_role = $request->id_role;
            $user->is_blocked = $request->is_blocked;
            $user->number_attempt = $request->number_attempt;

            if ($request->filled('password')) {
                $user->password = $request->password;
            }

            $user->save();
            return redirect()->route('admin')->with('success', 'Пользователь обновлён');
        } catch (\Exception $e) {
            return redirect()->route('admin')->with('error', 'Ошибка при обновлении пользователя: ' . $e->getMessage());
        }
    }
}