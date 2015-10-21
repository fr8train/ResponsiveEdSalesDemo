<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\User;
use App\Token;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

use Response;
use Validator;
use Api;
use Cache;
use Hash;

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

        $token = $this->getAdminToken($parentDomainId);

        // CREATE DOMAIN TO TEST AVAILABILITY
        $response = Api::post(array(
            'requests' => array(
                'domain' => array(
                    0 => array(
                        'name' => $domainName,
                        'userspace' => preg_replace("/[^0-9a-zA-Z]/", "", strtolower($domainName))
                    )
                )
            )
        ), "cmd=createdomains&parentid={$parentDomainId}&_token={$token}");

        $this->saveToken($response, $token);

        if (!$this->isOK($response))
            return $this->__response("Create domain failed", 500, (array)$response->response);
        else {
            if (isset($response->response->responses->response[0]->errorId)) {
                // ERROR WITH CREATE DOMAIN AT THIS POINT IS MOST LIKELY DOMAIN ALREADY EXISTS
                return $this->__response("Error with create domain: most likely domain already exists.", 403, (array)$response->response->responses->response[0]);
            } elseif (isset($response->response->responses->response[0]->code) &&
                $response->response->responses->response[0]->code == "OK"
            ) {
                // CREATE DOMAIN WORKED
                // DELETE DOMAIN
                $domainId = $response->response->responses->response[0]->domain->domainid;

                $response = Api::get("cmd=deletedomain&domainid=$domainId&_token={$this->token}");
                $this->saveToken($response, $this->token);

                return $this->__response("Domain is available.", 200);
            } else {
                return $this->__response("Unknown response", 500, (array)$response->response);
            }
        }
    }

    /**
     * Create domain
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreateDomain(Request $request)
    {

        if ($request->has("token")) {
            $token = $request->get("token");
        } else {
            $token = "";
            $decrypted = $request->get("parentDomainId") == 27986377 ? "bt_admin" : "ku_admin";

            if ($request->has("key") && Hash::check($decrypted, $request->get("key")))
                $token = $this->getAdminToken($request->get("parentDomainId"));
        }

        $resourceDomainId = $request->get("parentDomainId") == 27986377 ? 34204009 : 34204006;

        $domainSpace = preg_replace("/[^0-9a-zA-Z]/", "", strtolower($request->get('domainName')));

        $response = Api::post(array(
            'requests' => array(
                'domain' => array(
                    0 => array(
                        'name' => $request->get('domainName'),
                        'userspace' => $domainSpace,
                        'data' => array(
                            'resourcebase' => array(
                                'DomainId' => $resourceDomainId
                            )
                        )
                    )
                )
            )
        ), "cmd=createdomains&parentid={$request->get('parentDomainId')}&_token=$token");

        $this->saveToken($response, $token);

        if (!$this->isOK($response))
            return $this->__response("Create domain failed", 500, (array)$response->response);
        else {
            if (isset($response->response->responses->response[0]->errorId)) {
                // ERROR WITH CREATE DOMAIN AT THIS POINT IS MOST LIKELY DOMAIN ALREADY EXISTS
                return $this->__response("Error with create domain: most likely domain already exists.", 403, (array)$response->response->responses->response[0]);
            } elseif (isset($response->response->responses->response[0]->code) &&
                $response->response->responses->response[0]->code == "OK"
            ) {
                // CREATE DOMAIN WORKED

                return $this->__response("Domain created.", 200, array(
                    "userspace" => $domainSpace
                ));
            } else {
                return $this->__response("Unknown response", 500, (array)$response->response);
            }
        }
    }

    public function postCreateUsers(Request $request)
    {
        if ($request->has("token")) {
            $token = $request->get("token");
        } else {
            $token = "";
            $decrypted = $request->get("parentDomainId") == 27986377 ? "bt_admin" : "ku_admin";

            if ($request->has("key") && Hash::check($decrypted, $request->get("key")))
                $token = $this->getAdminToken($request->get("parentDomainId"));
        }

        $response = Api::post(array(
            'requests' => array(
                'user' => array(
                    array(
                        'username' => 'student',
                        'password' => 'password',
                        'firstname' => 'Test',
                        'lastname' => 'Student',
                        'email' => '',
                        'domainid' => "//{$request->get('userspace')}"
                    ), array(
                        'username' => "{$request->get('email')}",
                        'password' => "{$request->get('password')}",
                        'firstname' => "{$request->get('firstname')}",
                        'lastname' => "{$request->get('lastname')}",
                        'email' => "{$request->get('email')}",
                        'domainid' => "//{$request->get('userspace')}"
                    )
                )
            )
        ), "cmd=createusers2&_token=$token");

        if (!$this->isOK($response))
            return $this->__response("Create domain failed", 500, (array)$response->response);
        else {
            $this->saveToken($response, $token);

            if (isset($response->response->responses->response[0]->errorId)) {
                // ERROR WITH CREATE DOMAIN AT THIS POINT IS MOST LIKELY DOMAIN ALREADY EXISTS
                return $this->__response("Error with create domain: most likely domain already exists.", 403, (array)$response->response->responses->response[0]);
            } elseif (isset($response->response->responses->response[0]->code) &&
                $response->response->responses->response[0]->code == "OK"
            ) {
                // CREATE DOMAIN WORKED

                return $this->__response("Users created.", 200, array(
                    "student" => array(
                        'id' => $response->response->responses->response[0]->user->userid,
                        'username' => 'student',
                        'password' => 'password'
                    ),
                    "teacher" => array(
                        'id' => $response->response->responses->response[1]->user->userid,
                        'username' => "{$request->get('email')}",
                        'password' => "{$request->get('password')}"
                    )
                ));
            } else {
                return $this->__response("Unknown response", 500, (array)$response->response);
            }
        }
    }

    public function postEnrollUsers(Request $request)
    {
        if ($request->has("token")) {
            $token = $request->get("token");
        } else {
            $token = "";
            $decrypted = $request->get("parentDomainId") == 27986377 ? "bt_admin" : "ku_admin";

            if ($request->has("key") && Hash::check($decrypted, $request->get("key")))
                $token = $this->getAdminToken($request->get("parentDomainId"));
        }

        $courseDomainSpace = $request->get("parentDomainId") == 27986377 ? "btcourses" : "kucourses";

        $usersToBeCopied = array();
        $response = Api::get("cmd=listusers&domainid=//$courseDomainSpace&_token=$token");
        $this->saveToken($response, $token);

        foreach ($response->response->users->user as $user) {
            $usersToBeCopied[$user->username] = (object)array(
                'id' => $user->id,
                'enrollments' => array()
            );

            $response2 = Api::get("cmd=listuserenrollments&userid={$user->id}&_token=$token");

            foreach ($response2->response->enrollments->enrollment as $enrollment) {
                $usersToBeCopied[$user->username]->enrollments[$enrollment->courseid] = $enrollment->privileges;
            }
        }

        $courseDictionary = array();

        foreach ($usersToBeCopied as $userType => $userToBeCopied) {
            foreach ($userToBeCopied->enrollments as $courseId => $rights) {
                // IF NEW COURSE DOESN'T EXIST WE NEED TO MAKE IT
                if (!array_key_exists($courseId, $courseDictionary)) {
                    $response = Api::post(array(
                        'requests' => array(
                            'course' => array(
                                array(
                                    'courseid' => $courseId,
                                    'domainid' => "//{$request->get('userspace')}",
                                    'action' => 'DerivativeChildCopy'
                                )
                            )
                        )
                    ), "cmd=copycourses&_token=$token");
                    $this->saveToken($response, $token);

                    $courseDictionary[$courseId] = $response->response->responses->response[0]->course->courseid;
                }

                $response = Api::post(array(
                    'requests' => array(
                        'enrollment' => array(
                            array(
                                'userid' => $request->get("{$userType}Id"),
                                'entityid' => $courseDictionary[$courseId],
                                'flags' => $rights,
                                'status' => 1
                            )
                        )
                    )
                ), "cmd=createenrollments&_token=$token");
            }
        }

        return $this->__response("Finished enrollment creation.");
    }

    /**
     * Checks to see if response has status code of "OK"
     *
     * @param \stdClass $response
     * @return bool
     */
    private function isOK(\stdClass &$response)
    {
        return $response->response->code == "OK";
    }

    /**
     * Exchanges the parentDomainId for an authentication token for the admin account for that domain ID
     *
     * @param int $parentDomainId
     * @return string
     */
    private function getAdminToken($parentDomainId)
    {
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

        return $token;
    }

    /**
     * If code="OK" and we have a "_token" update it
     *
     * @param \stdClass $response
     * @param $token
     */
    private function saveToken(\stdClass &$response, $token)
    {
        if ($this->isOK($response) &&
            isset($response->response->_token)
        ) {
            $token = Token::where('token', $token)->first();
            $token->token = $response->response->_token;
            $this->token = $response->response->_token;
            $token->updated_at = Carbon::now()->toDateTimeString();
            $token->save();
        }
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

        $this->token = $user->token->token;
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

    public function getAllDomains()
    {
        if ($this->isAuthenticated()) {
            $domains = array();
            // GET ALL BTDEMO DOMAINS
            $result = Api::get("cmd=listdomains&domainid=//btdemo&_token={$this->token}");
            $this->saveToken($result, $this->token);

            $domains['btdemo'] = $result->response->domains->domain;

            // GET ALL KUDEMO DOMAINS
            $result = Api::get("cmd=listdomains&domainid=//kudemo&_token={$this->token}");
            $this->saveToken($result, $this->token);

            $domains['kudemo'] = $result->response->domains->domain;

            return $this->__response("Domains gathered.", 200, $domains);
        } else {
            return $this->__response("User is not authenticated.", 401);
        }
    }

    public function getDomain($id)
    {
        if ($this->isAuthenticated()) {
            $response = array();
            $result = Api::get("cmd=getdomain2&domainid=$id&_token={$this->token}");
            $this->saveToken($result, $this->token);

            $response['domain'] = $result->response->domain;

            $result = Api::get("cmd=listusers&domainid=$id&_token={$this->token}");
            $this->saveToken($result, $this->token);

            if (isset($result->response->users->user))
                $response['users'] = $result->response->users->user;
            else
                $response['users'] = $result->response->users;

            return $this->__response("Info for Domain (ID=$id) gathered.", 200, $response);
        } else {
            return $this->__response("User is not authenticated.", 401);
        }
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
