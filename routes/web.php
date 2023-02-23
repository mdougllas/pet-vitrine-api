<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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
// Route::fallback(function () {
//     return redirect()->away(config('services.frontend.root'));
// });

Route::get('test', function () {
    return response()->json([
        'Test ready' => true
    ]);
});

Route::get('logs', function () {
    $files = collect(Storage::files("public/spider"));

    return view('logs', ['files' => $files]);
});

Route::get('download/public/spider/{file}', function (string $file) {
    return Storage::download("public/spider/$file");
});
