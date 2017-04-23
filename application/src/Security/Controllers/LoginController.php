<?php

namespace Application\Security\Controllers;

use Phalcon\Mvc\Controller;

/**
 * Class LoginController
 * @package Application\Security\Controllers
 *
 */
class LoginController extends Controller
{
    /**
     * @SWG\POST(
     *   path="/login",
     *   summary="Login",
     *   produces={"application/json"},
     *     @SWG\Parameter(
     *     in="formData",
     *     type="string",
     *     name="email",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/User")
     *   ),
     *     @SWG\Parameter(
     *     in="formData",
     *     type="string",
     *     name="password",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/User")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Returns a JWT token for authorization"
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Not found User, Invalid password"
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="Validation of formData failed"
     *   )
     * )
     * @return string
     */
    public function loginAction()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        return $email . $password;
    }
}