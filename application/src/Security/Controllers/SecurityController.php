<?php
/**
 * Created by PhpStorm.
 * User: juliuskoronci
 * Date: 17/04/2017
 * Time: 16:38
 */

namespace Application\Security\Controllers;


use Application\Security\Model\User\User;
use Phalcon\Mvc\Controller;

/**
 * Class SecurityController
 * @package Application\Security\Controllers
 */
abstract class SecurityController extends Controller
{
    /**
     * @var User
     */
    private $user;
    /**
     * We will use initialize to check for a Token, Validate it and retrieve the current user
     */
    public function initialize()
    {

    }
}