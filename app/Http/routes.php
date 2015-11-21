<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

/**
 * Companies
 */
Route::group(['namespace' => 'Companies', 'prefix' => 'companies'], function() {

    //\Auth

    Route::group(['namespace' => 'Auth'], function() {
        Route::group(['prefix' => 'auth'], function () {
            // Authentication routes...
            Route::post('login', ['as' => 'companies.auth.login','uses'=>'AuthController@postLogin']);

            // Registration routes...
            Route::post('register', ['as' => 'companies.auth.register', 'uses'=>'AuthController@postRegister']);

        });

        Route::group(['prefix' => 'password'], function () {
            // Password reset link request routes...
            Route::post('email', ['as' => 'companies.auth.email', 'uses'=>'PasswordController@postEmail']);

            // Password reset routes...
            Route::post('reset', ['as' => 'companies.auth.reset', 'uses'=>'PasswordController@postReset']);
        });
    });

    Route::resource('employees', 'EmployeesController');

    //example
    Route::resource('example', 'ExampleController', ['except' => ['create', 'edit']]);
});


/**
 * Employees
 */
Route::group(['namespace' => 'Employees', 'prefix' => 'employees'], function() {
    //\Auth

    Route::group(['namespace' => 'Auth'], function() {
        Route::group(['prefix' => 'auth'], function () {
            // Authentication routes...
            Route::post('login', ['as' => 'companies.auth.login', 'uses'=>'AuthController@postLogin']);

            // Registration routes...
            //Route::post('register', ['as' => 'companies.auth.register', 'uses'=>'AuthController@postRegister']); //the registration is managed by the company

        });

        Route::group(['prefix' => 'password'], function () {
            // Password reset link request routes...
            Route::post('email', ['as' => 'companies.auth.email', 'uses'=>'PasswordController@postEmail']);

            // Password reset routes...
            Route::post('reset', ['as' => 'companies.auth.reset', 'uses'=>'PasswordController@postReset']);
        });
    });

    Route::resource('employees', 'EmployeesController');
    Route::resource('groups', 'GroupsController');
});