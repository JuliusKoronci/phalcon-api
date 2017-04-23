<?php

namespace Application\Security\Plugins;

use Igsem\ApiExceptions\Exceptions\TokenException;
use Application\Security\Utils\Firewall;
use Application\Security\Utils\JWT;
use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
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
            $this->handleSecurityCheck($jwt, $app);
        }

        return true;
    }

    private function handleSecurityCheck($jwt, $app)
    {
        $secure = $jwt->isValid($app->request->getHeader('Authorization'));
        if (!$secure) {
            throw new TokenException(null, null, null, [
                'context' => 'Thrown in Auth plugin while validating JWT'
            ]);
        }
    }
}