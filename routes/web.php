<?php

/** @var Router $router */

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

use Illuminate\Routing\Router;

Route::get('/', 'SmaregiAuthAction@authorizeUser');
Route::get('/dashboard', 'SmaregiAuthAction@dashboard')->name('dashboard');
$router->get(
    '/logout',
    function () {
        auth()->logout();

        return redirect()->to('/');
    }
)->name('logout');
