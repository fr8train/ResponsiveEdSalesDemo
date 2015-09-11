<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;
use Response;

class ControllerWrapper extends Controller
{
    /**
     * Fails validation response format
     *
     * @param \Illuminate\Support\MessageBag $errors
     * @return \Illuminate\Http\Response $response
     */
    public function failsValidation(MessageBag $errors)
    {
        return Response::make(json_encode(array(
            'message' => 'Request failed validation.',
            'errors' => $errors
        )), 400);
    }
}
