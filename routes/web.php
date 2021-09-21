<?php

declare(strict_types=1);

/** @var Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Laravel\Lumen\Routing\Router;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'oauth'], function () use ($router) {
    $router->group(['prefix' => 'msc'], function () use ($router) {
        $router->get('auth', 'MscAuthController@auth');
        $router->get('code', 'MscAuthController@code');
    });
    $router->group(['prefix' => 'zen'], function () use ($router) {
        $router->get('auth', 'ZenAuthController@auth');
        $router->get('code', 'ZenAuthController@code');
    });
});
