<?php

namespace Application\Security\Plugins;

use Application\Security\Utils\Firewall;
use Application\Security\Utils\JWT;
use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Mvc\User\Plugin;

/**
 * Created by PhpStorm.
 * User: juliuskoronci
 * Date: 17/04/2017
 * Time: 16:48
 */
class AuthPlugin extends Plugin
{
    public function beforeExecuteRoute(Event $event, Micro $app)
    {
        $path = $app->getRouter()->getMatchedRoute()->getCompiledPattern();
        $firewall = new Firewall($this->di);
        $jwt = new JWT($this->di);
        if ($firewall->shouldSecure($path)) {
            return $jwt->isValid($app->request->getHeader('Authorization'));
        }

        return true;
    }
}