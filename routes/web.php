<?php

/** @var \Laravel\Lumen\Routing\Router $router */
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

//auth
$router->group(['prefix' =>'auth'], function () use ($router){
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
});

//Gaji
Route::group(['middleware' => ['auth']], function ($router){
    $router->get('/Gaji', 'GajiController@index');
    $router->post('/Gaji', 'GajiController@store');
    $router->get('/Gaji/{id}', 'GajiController@show');
    $router->put('/Gaji/{id}', 'GajiController@update');
    $router->delete('/Gaji/{id}', 'GajiController@destroy');
});

//post public
$router->get('/public/Pegawai', 'PegawaiPublicController@index');
$router->get('/public/Pegawai/{id}', 'PegawaiPublicController@show');

//Pegawai
Route::group(['middleware' => ['auth']], function ($router){
    $router->get('/Pegawai', 'PegawaiController@index');
    $router->post('/Pegawai', 'PegawaiController@store');
    $router->get('/Pegawai/{id}', 'PegawaiController@show');
    $router->put('/Pegawai/{id}', 'PegawaiController@update');
    $router->delete('/Pegawai/{id}', 'PegawaiController@destroy');
});

//jabatan
Route::group(['middleware' => ['auth']], function ($router){
    $router->get('/Jabatan', 'JabatanController@index');
    $router->post('/Jabatan', 'JabatanController@store');
    $router->get('/Jabatan/{id}', 'JabatanController@show');
    $router->put('/Jabatan/{id}', 'JabatanController@update');
    $router->delete('/Jabatan/{id}', 'JabatanController@destroy');
});

//profiles
Route::group(['middleware' => ['auth']], function ($router){
    $router->get('/Profiles', 'ProfilesController@index');
    $router->post('/Profiles', 'ProfilesController@store');
    //$router->get('/Profiles/{id}', 'ProfilesController@show');
    //$router->put('/Profiles/{id}', 'ProfilesController@update');
    $router->get('/Profiles/image/{imageName}', 'ProfilesController@image');
    //$router->delete('/Profiles/{id}', 'ProfilesController@destroy');
});