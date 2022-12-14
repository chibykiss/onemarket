<?php

use App\Http\Controllers\v1\ForgotPasswordController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

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

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
});
Route::post('/reset-password', [ForgotPasswordController::class, 'resetpassword'])->name('password.reset');
Route::get('/', function(){
    Artisan::call('cache:clear');
    return view('welcome');
});