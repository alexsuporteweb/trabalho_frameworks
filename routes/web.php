<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('user')->group(function () {
    Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('user-index');
    Route::get('/create', [App\Http\Controllers\UserController::class, 'create'])->name('user-create');
    Route::post('/create', [App\Http\Controllers\UserController::class, 'store'])->name('user-store');
    Route::get('/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('user-edit');
    Route::post('/update/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('user-update');
    Route::get('/delete/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->name('user-delete');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__ . '/auth.php';
