<?php

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
$router->post('/register','AuthController@register');
$router->post('/login','AuthController@login');
$router->get('/logout','AuthController@logout');

$router->get('/token/{token}','AuthController@token');

$router->post('/resendemail','AuthController@resendemail');
$router->get('/verifyemail','AuthController@verifyemail');

$router->post('/forgotpassword','AuthController@forgotpassword');
$router->get('/cekforgotpassword','AuthController@cekforgotpassword');
$router->post('/resetpassword','AuthController@resetpassword');

// $router->group(['prefix' => 'role'], function () use ($router) {
$router->group(['prefix' => 'role', 'middleware' => 'auth'], function () use ($router) {
	$router->get('/','RoleController@index');
	$router->post('store','RoleController@store');
	$router->get('show/{id}','RoleController@show');
	$router->get('permission','RoleController@permission');
	$router->patch('update/{id}','RoleController@update');
	$router->delete('destroy/{id}','RoleController@destroy');
});