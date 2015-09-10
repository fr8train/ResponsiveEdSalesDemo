<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use Response;
use Api;

class DlapController extends Controller
{
    public function postLogin() {
        $input = Input::all();
    }

    public function postCheckDomainAvailability() {
        $DomainName = Input::get('DomainName');
        $ParentDomainId = Input::get('ParentDomainId');

        $result = Api::post(array(

        ));
    }
}
