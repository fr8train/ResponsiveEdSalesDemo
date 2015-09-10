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

    public function getBrightThinker()
    {
        return view('register.index',array(
            'brand' => 'brightthinker'
        ));
    }
}
