<?php

use App\Http\Controllers\Api\DlapController;

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

Route::controller('register', 'Register\\RegisterController');
Route::controller('dlap', 'Api\\DlapController');
Route::controller('auth', 'Auth\\AuthController');

Route::get('/', function () {
    $dlap = new DlapController();

    if ($dlap->isAuthenticated()) {
        return "Logged in.";
    } else {
        return redirect('auth/login');
    }
});
