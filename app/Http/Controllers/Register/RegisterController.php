<?php

namespace App\Http\Controllers\Register;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function getKnowledgeu()
    {
        return view('register.index',array(
            'brand' => 'knowledgeu'
        ));
    }

    public function getBrightthinker()
    {
        return view('register.index',array(
            'brand' => 'brightthinker'
        ));
    }
}
