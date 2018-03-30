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

Auth::login(\App\User::find(1));

// Route::get('/', function () {
//     return view('test');
// });

// PUBLIC ROUTES
Route::view('/test', 'test');
Route::view('/design', 'design');
Route::get('/', 'PublicController@events');

Route::get('/t', 'PublicController@themes');
Route::get('/e', 'PublicController@events');
Route::get('/v', 'PublicController@venues');
Route::get('/c/{slug}', 'PublicController@company');
Route::get('/user/{id}', 'PublicController@user');

Route::get('/theme/{id}', 'PublicController@showTheme');
Route::get('/event/{id}', 'PublicController@showEvent');
Route::get('/venue/{id}', 'PublicController@showVenue');

// Data routes
Route::get('/data/eventlist', 'DataController@eventList');
Route::post('/data/relatedeventlist', 'DataController@relatedEventList');
Route::get('/data/getcompany/{id}', 'DataController@getCompany');
Route::post('/data/getcomments', 'DataController@getComments');

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

// USER ROUTES
Route::group(['namespace' => 'Users', 'prefix' => 'users',  'middleware' => 'auth'], function () {

	Route::get('/', 'UserController@index');
	Route::get('/order', 'UserController@order');

	Route::post('/follow', 'UserController@follow');

	Route::post('/like/theme', 'LikeController@toggleTheme');
	Route::post('/like/venue', 'LikeController@toggleVenue');

	// Comments
	Route::post('/comment/add', 'CommentController@commentTheme');
	Route::post('/comment/remove', 'CommentController@destroy');

});

Route::group(['namespace' => 'Publishers', 'prefix' => 'dashboard',  'middleware' => 'auth'], function () {

	Route::get('/', 'DashboardController@index');
	Route::resource('themes', 'ThemeController');
	Route::resource('events', 'EventController');
	Route::resource('teachers', 'TeacherController');
	Route::resource('venues', 'VenueController');
	Route::resource('orders', 'OrderController');
	Route::resource('comments', 'CommentController');

});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin',  'middleware' => ['auth', 'admin']], function () {

	Route::get('/activity', 'AdminController@activity');

});