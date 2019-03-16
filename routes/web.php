<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('users/store/', [
	'uses' => 'UsersController@store'
]);

Route::post('/user_reservations', [
	'uses' => 'UserReservationsController@store'
]);

Route::get('/user_reservations', [
	'uses' => 'UserReservationsController@index'
]);

Route::get('/meals', [
	'uses' => 'MealsController@getRandomMeal'
])->name('meals');

Route::get('/drinks/{type?}', [
	'uses' => 'DrinksController@getDrink'
])->name('drinks');

Route::post('/user_reservations/get_next_available', [
	'uses' => 'UserReservationsController@getNextAvailable'
]);