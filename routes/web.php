<?php

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

Route::get('login', function () {
    return redirect()->away(config('services.frontend.login'));
})->name('login');

/***
 * Redirect any route non /api to front end
 */
Route::fallback(function () {
    return redirect()->away(config('services.frontend.root'));
});

Route::get('test', function () {
    return response()->json([
        'Test ready' => true
    ]);
});

Route::get('logs', function () {
    $today = today()->format('m-d-Y');
    $file = \Illuminate\Support\Facades\Storage::download("public/spider/$today.log");

    return $file;
});
