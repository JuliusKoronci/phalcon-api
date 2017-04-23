<?php

namespace Application\Security\Model\User;

use Phalcon\Mvc\Model;

/**
 * @SWG\Definition(required={"email", "name", "password"}, type="object", @SWG\Xml(name="User"))
 */
class User extends Model
{
    /**
     * @SWG\Property(type="string", description="UUID")
     * @var int
     */
    public $id;

    /**
     * @SWG\Property(type="string")
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @SWG\Property(type="string")
     * @var string
     */
    public $password;
}