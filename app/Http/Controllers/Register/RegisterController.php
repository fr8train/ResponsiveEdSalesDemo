<?php

namespace App\Http\Controllers\Register;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Hash;

class RegisterController extends Controller
{
    public function getKnowledgeU()
    {
        return view('register.index',array(
            'brand' => 'knowledgeu'
        ));
    }

    public function postKnowledgeU(Request $request)
    {
        $input = $request->all();

        if (isset($input['_token']))
            unset($input['_token']);

        $input['brand'] = "knowledgeu";
        $input['parent_domain_id'] = 27986474;
        $input['key'] = Hash::make("ku_admin");

        return view('register.process',$input);
    }

    public function getBrightThinker()
    {
        return view('register.index',array(
            'brand' => 'brightthinker'
        ));
    }

    public function postBrightThinker(Request $request)
    {
        $input = $request->all();

        if (isset($input['_token']))
            unset($input['_token']);

        $input['brand'] = "brightthinker";
        $input['parent_domain_id'] = 27986377;
        $input['key'] = Hash::make("bt_admin");

        return view('register.process',$input);
    }
}
