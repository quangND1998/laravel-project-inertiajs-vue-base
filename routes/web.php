<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapBoxController;
use Inertia\Inertia;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/auth/login');
});

Route::get('auth/login', function () {
    return Inertia::render('Nestjs/Login');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('chat', [ChatController::class, 'index']);
    Route::get('fecthMessages', [ChatController::class, 'fetchMessage']);
    Route::post('sendMessage', [ChatController::class, 'sendMesage']);
});
Route::get('maps', [MapBoxController::class, 'index']);

Route::get('/dashboard', function () {
    return Inertia::render('DashBoard');
})->middleware(['auth'])->name('dashboard');


require __DIR__ . '/auth.php';
