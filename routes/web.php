<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');

    $router->post('/logout', [
        'middleware' => 'auth',
        'uses' => 'AuthController@logout'
    ]);
});

$router->group(['middleware' => 'auth', 'prefix' => 'api'], function () use ($router) {
    $router->get('/role', 'RoleController@index');
    $router->get('/role/{id}', 'RoleController@show');
    $router->post('/role', 'RoleController@create');
    $router->put('/role/{id}', 'RoleController@update');
    $router->delete('/role/{id}', 'RoleController@delete');

    $router->get('/member', 'MemberController@index');
    $router->get('/member/{id}', 'MemberController@show');
    $router->post('/member', 'MemberController@create');
    $router->put('/member/{id}', 'MemberController@update');
    $router->delete('/member/{id}', 'MemberController@delete');

    $router->get('/transaksi', 'TransaksiController@index');
    $router->get('/transaksi/{id}', 'TransaksiController@show');
    $router->post('/transaksi', 'TransaksiController@create');
    $router->put('/transaksi/{id}', 'TransaksiController@update');
    $router->delete('/transaksi/{id}', 'TransaksiController@delete');
    
    $router->get('/histori-transaksi', 'HistoriTransaksiController@index');

    $router->get('/profile/{id}', 'ProfileController@show');
    $router->put('/profile/{id}', 'ProfileController@update');
});
