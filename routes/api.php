<?php

use Illuminate\Routing\Router;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/** @var Router $router */

$router->group(
    ['middleware' => 'callback'],
    function (Router $router) {
        $router->get(env('SMAREGI_REDIRECT_URL', '/smaregi/auth/callback'), 'SmaregiAuthAction');
    }
);
