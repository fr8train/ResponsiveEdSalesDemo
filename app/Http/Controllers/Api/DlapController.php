<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ControllerWrapper;
use Illuminate\Http\Request;

use App\Http\Requests;
use Response;
use Validator;
use Api;

class DlapController extends ControllerWrapper
{
    public function postLogin(Request $request) {
        // VALIDATION RULE
        $validator = Validator::make($request->all(),[
            'domainName' => 'required',
            'username' => 'required',
            'password' => 'required'
        ]);

        // VALIDATION FAIL
        if ($validator->fails()) {
            return $this->failsValidation($validator->errors());
        }
    }

    public function postCheckDomainAvailability(Request $request) {
        // VALIDATION RULE
        $validator = Validator::make($request->all(),[
            'domainName' => 'required',
            'parentDomainId' => 'required'
        ]);

        // VALIDATION FAIL
        if ($validator->fails()) {
            return $this->failsValidation($validator->errors());
        }

        // GET INPUT
        $domainName = $request->input('domainName');
        $parentDomainId = $request->input('parentDomainId');
    }


}
