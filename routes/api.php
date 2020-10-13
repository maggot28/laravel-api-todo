<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'AuthController@logout');
    });
});

Route::group(['prefix' => 'task'], function () {
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('/get/{task?}', 'TaskController@get');
        Route::post('/create', 'TaskController@create');
        Route::post('/sync', 'TaskController@sync');
        Route::patch('/update/{task_id}', 'TaskController@patch');
        Route::put('/update/{task_id}', 'TaskController@update');
        Route::delete('/delete/{task_id}', 'TaskController@delete');
        Route::delete('/delete/{task_id}/forever', 'TaskController@destroy');
    });
});

Route::group(['prefix' => 'user'], function () {
    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('/', 'UserController@get');
        Route::put('/update', 'UserController@update');
        Route::get('/settings', 'UserController@getSettings');
    });
});
