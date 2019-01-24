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
$router->get('/cek','AuthController@cek');

$router->get('/token/{token}','AuthController@token');

$router->post('/resendemail','AuthController@resendemail');
$router->get('/verifyemail','AuthController@verifyemail');

$router->post('/forgotpassword','AuthController@forgotpassword');
$router->get('/cekforgotpassword','AuthController@cekforgotpassword');
$router->post('/resetpassword','AuthController@resetpassword');