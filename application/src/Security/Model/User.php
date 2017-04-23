<?php

namespace Application\Security\Model\User;

use Phalcon\Mvc\Model;

/**
 * @SWG\Definition(required={"email", "name", "password"}, type="object", @SWG\Xml(name="User"))
 */
class User extends Model
{
    /**
     * @SWG\Property(name="id", type="string", description="UUID")
     * @var int
     */
    public $id;

    /**
     * @SWG\Property(name="name", type="string")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(name="email", type="string")
     * @var string
     */
    public $email;

    /**
     * @SWG\Property(name="password", type="string")
     * @var string
     */
    public $password;
}