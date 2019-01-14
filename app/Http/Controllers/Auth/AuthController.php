<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Api\DlapController;
use Illuminate\Http\Request;
use Cache;
use Api;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    private $dlap;
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    public function __construct() {
        $this->dlap = new DlapController();
    }

    public function getLogin() {
        return view('auth.index');
    }

    public function postLogin(Request $request) {
        //var_dump($request->all());

        if ($request->get('domain') == "blue-demo") {
            if (!$this->dlap->isAuthenticated($request->get('username'))) {
                $result = $this->dlap->postLogin(new Request(array(
                    'domainName' => $request->get('domain'),
                    'username' => $request->get('username'),
                    'password' => $request->get('password')
                )));
                $response = $result->getData();

                if ($response->payload->code != "OK") {
                    return redirect('auth/login')->with('error','Login unsuccessful.');
                }

                // CHECK FOR ADMIN LEVEL RIGHTS ON RESPONSIVE ED DOMAIN
                $result = Api::get("cmd=getrights&actorid={$response->payload->user->userid}&entityid=71102460&_token={$response->payload->user->token}");
                if ($result->response->code != "OK" ||
                    $result->response->rights->flags != "-1") {
                    header("Location: http://{$request->get('domain')}.agilixbuzz.com/");
                    die();
                }

                Cache::forever('username',$request->get('username'));
            }

            return redirect('/');
        } else {
            header("Location: http://{$request->get('domain')}.agilixbuzz.com/");
            die();
        }
    }

    public function getLogout() {
        Cache::forget('username');
        return redirect('auth/login');
    }
}
