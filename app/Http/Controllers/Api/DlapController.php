<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\User;
use App\Token;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

use Response;
use Validator;
use Api;
use Cache;

class DlapController extends Controller
{
    protected $token;

    /**
     * Main Login Function
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        // VALIDATION RULE
        $validator = Validator::make($request->all(), [
            'domainName' => 'required',
            'username' => 'required',
            'password' => 'required'
        ]);

        // VALIDATION FAIL
        if ($validator->fails()) {
            return $this->failsValidation($validator->errors());
        }

        $response = Api::post(array(
            'request' => array(
                'cmd' => 'login',
                'username' => "{$request->get('domainName')}/{$request->get('username')}",
                'password' => "{$request->get('password')}"
            )
        ));

        switch ($response->response->code) {
            case 'OK':
                // UPDATE USER INFO OR CREATE NEW USER
                // STORE OR CREATE TOKEN
                // RETURN $RESPONSE->RESPONSE OBJECT
                $user = User::where('username', $request->get('username'))
                    ->with('token')
                    ->first();
                $_user = $response->response->user;

                if (is_null($user))
                    $user = new User();

                // ASSIGN USER VARIABLES
                $user->brainhoney_user_id = $_user->userid;
                $user->username = $_user->username;
                $user->firstname = $_user->firstname;
                $user->lastname = $_user->lastname;
                $user->email = $_user->email;
                $user->domain_id = $_user->domainid;
                $user->domain_space = $_user->userspace;
                $user->domain_name = $_user->domainname;

                $user->save();

                // END OF USER UPDATE/CREATE
                if (is_null($user->token)) {
                    $token = new Token();

                    $token->brainhoney_user_id = $user->brainhoney_user_id;
                    $token->token = $_user->token;
                    $token->lifespan = $_user->authenticationexpirationminutes;

                    $token->save();
                } else {
                    $user->token->token = $_user->token;
                    $this->token = $_user->token;
                    $user->token->lifespan = $_user->authenticationexpirationminutes;

                    $user->token->save();
                }

                return $this->__response("Successful login", 200, (array)$response->response);
            case 'InvalidCredentials':
                return $this->__response($response->response->message, 401, (array)$response->response);
            default:
                return $this->__response("New response code: {$response->response->code}", 500, (array)$response->response);
        }
    }

    /**
     * Check to see if a Domain exists that would be available under the chosen umbrella brand
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCheckDomainAvailability(Request $request)
    {
        // VALIDATION RULE
        $validator = Validator::make($request->all(), [
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

        // FIGURE OUT WHICH ADMIN USER WE NEED TO USE
        $admin = $parentDomainId == 27986377 ? env('BT_ADMIN_USER') : env('KU_ADMIN_USER');
        $adminDomain = $parentDomainId == 27986377 ? env('BT_ADMIN_DOMAIN') : env('KU_ADMIN_DOMAIN');
        $adminPassword = $parentDomainId == 27986377 ? env('BT_ADMIN_PASS') : env('KU_ADMIN_PASS');

        $token = "";

        if (!$this->isAuthenticated($admin)) {
            // IF ADMIN IS NOT AUTHENTICATED
            // LET'S AUTHENTICATE THEM

            $loginRequest = new Request(array(
                'domainName' => $adminDomain,
                'username' => $admin,
                'password' => $adminPassword
            ));

            $result = $this->postLogin($loginRequest);
            $json = $result->getData();

            $token = $json->payload->_token;
        } else {
            $user = User::where('username', $admin)
                ->with('token')->first();

            $token = $user->token->token;
        }

        // CREATE DOMAIN TO TEST AVAILABILITY
        $response = Api::post(array(
            'requests' => array(
                'domain' => array(
                    0 => array(
                        'name' => $domainName,
                        'userspace' => preg_replace("/[^0-9a-zA-Z]/","",strtolower($domainName))
                    )
                )
            )
        ), "cmd=createdomains&parentid={$parentDomainId}&_token={$token}");

        $response = $this->saveToken($response, $token);

        if ($response->response->code != "OK")
            return $this->__response("Create domain failed", 500, (array)$response->response);
        else {
            if (isset($response->response->responses->response[0]->errorId)) {
                // ERROR WITH CREATE DOMAIN AT THIS POINT IS MOST LIKELY DOMAIN ALREADY EXISTS
                return $this->__response("Error with create domain: most likely domain already exists.", 403, (array)$response->response->responses->response[0]);
            } elseif (isset($response->response->responses->response[0]->code) &&
                $response->response->responses->response[0]->code == "OK") {
                // CREATE DOMAIN WORKED
                // DELETE DOMAIN
                $domainId = $response->response->responses->response[0]->domain->domainid;

                $response = Api::get("cmd=deletedomain&domainid=$domainId&_token={$this->token}");
                $this->saveToken($response, $this->token);

                return $this->__response("Domain exists", 200);
            } else {
                return $this->__response("Unknown response", 500, (array)$response->response);
            }
        }
    }

    /**
     * If code="OK" and we have a "_token" update it
     *
     * @param \stdClass $response
     * @param $token
     * @return \stdClass
     */
    private function saveToken(\stdClass &$response, $token)
    {
        if ($response->response->code == "OK" &&
            isset($response->response->_token)
        ) {
            $token = Token::where('token', $token)->first();
            $token->token = $response->response->_token;
            $this->token = $response->response->_token;
            $token->save();
        }
        return $response;
    }

    /**
     * public wrapper for authentication check by username
     *
     * @param string|null $username
     * @return bool
     */
    public function isAuthenticated($username = null)
    {
        // FIRST CHECK CACHE FOR USERNAME
        if (Cache::has('username')) {
            // LOOK UP USERNAME IN USERS.TOKENS TO SEE IF USER IS AUTHENTICATED
            // RETURN FALSE IF:
            //   USER DOESN'T EXIST IN USERS TABLE
            //   NO TOKEN FOR THIS USER
            //   TOKEN REFRESH FAILS
            return $this->checkUsernameForAuthentication(Cache::get('username'));
        } elseif (!is_null($username)) {
            // SAME AS ABOVE BUT USING THE PROVIDED USERNAME INSTEAD OF A CACHED ONE
            return $this->checkUsernameForAuthentication($username);
        }

        return false;
    }

    /**
     * Checks to see if User by {username} is authenticated
     *
     * @param $username
     * @return bool
     */
    private function checkUsernameForAuthentication($username)
    {
        $user = User::with('token')
            ->where('username', $username)
            ->first();

        if (is_null($user)) // NO USER EXISTS ON OUR SIDE
            return false;

        if (is_null($user->token)) // NO TOKEN FOR THIS USER
            return false;

        if ($this->hasExpired($user->token->updated_at, $user->token->lifespan)) // TOKEN EXISTS BUT IS EXPIRED
            return false;

        return true;
    }

    /**
     * Checks to see if now() > updated_at + lifespan
     *
     * @param $updated_at
     * @param $mins
     * @return bool
     */
    private function hasExpired($updated_at, $mins)
    {
        return strtotime('now') > strtotime("{$updated_at} + {$mins} minutes");
    }

    /**
     * Fails validation response format
     *
     * @param MessageBag $errors
     * @return \Illuminate\Http\Response
     */
    public function failsValidation(MessageBag $errors)
    {
        return $this->__response('Request failed validation.',
            400, $errors->all());
    }

    /**
     * Basic response wrapper - centralize JSON response formatting
     *
     * @param $message
     * @param int $httpCode
     * @param array|null $payload
     * @return \Illuminate\Http\Response
     */
    public function __response($message, $httpCode = 200, array $payload = null)
    {
        $response = array(
            'message' => $message
        );

        if (!is_null($payload))
            $response['payload'] = $payload;

        return Response::json($response, $httpCode);
    }
}
