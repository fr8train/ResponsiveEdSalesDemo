<?php

use App\Http\Controllers\Api\DlapController;
use App\User;

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

    if (Cache::has('username') && (Cache::get('username') == 'btadmin' || Cache::get('username') == 'kuadmin')) {
        Cache::forget('username');
    }

    if ($dlap->isAuthenticated()) {
        $user = User::where('username', Cache::get('username'))
            ->with('token')
            ->first();
        if ($user->domain_space == "blue-demo")
            return view('home', array(
                'user' => $user
            ));
        else return redirect('auth/login');
    } else {
        return redirect('auth/login');
    }
});
