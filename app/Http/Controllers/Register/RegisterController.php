<?php

namespace App\Http\Controllers\Register;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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

        $input['brand'] = 'knowledgeu';

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

        $input['brand'] = 'brightthinker';

        return view('register.process',$input);
    }
}
