<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->id_role === 2) {
        return redirect()->route('admin');
    }
    return view('dashboard', ['login' => $user->login]);
})->middleware('auth')->name('dashboard');

Route::get('/admin', [AdminController::class, 'index'])->middleware('auth')->name('admin');
Route::post('/users/store', [AdminController::class, 'store'])->middleware('auth')->name('users.store');
Route::put('/users/update', [AdminController::class, 'update'])->middleware('auth')->name('users.update');