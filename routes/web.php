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

$router->get('/', function () {
    return 'WALLET-API';
});

$router->group(['prefix' => 'clients'], function () use ($router) {
    $router->get('/', 'ClientController@index');
    $router->get('/{id}', 'ClientController@show');
    $router->post('/', 'ClientController@create');
    $router->put('/{id}', 'ClientController@update');
    $router->delete('/{id}', 'ClientController@delete');
});

$router->group(['prefix' => 'transactions'], function () use ($router) {
    $router->get('/', 'TransactionController@index');
    $router->get('/{id}', 'TransactionController@show');
    $router->post('/', 'TransactionController@create');
    $router->post('/{id}', 'TransactionController@refound');
});

$router->group(['prefix' => 'wallets'], function () use ($router) {
    $router->get('/{id}', 'WalletController@show');
    $router->post('/', 'WalletController@create');
});
