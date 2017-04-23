<?php
/**
 * Created by PhpStorm.
 * User: juliuskoronci
 * Date: 17/04/2017
 * Time: 20:02
 */

namespace Application\Security\Utils;


use Application\Security\Utils\KeyLoader\OpenSSLKeyLoader;
use Phalcon\Di;
use Firebase\JWT\JWT as FJTW;

class JWT
{
    /** @var array */
    private $jwt;

    private $keyLoader;

    private $alg = 'HS512';

    /**
     * Firewall constructor.
     * @param Di $di
     */
    public function __construct(Di $di)
    {
        $this->jwt = $di->get('jwt');
        $this->keyLoader = new OpenSSLKeyLoader($this->jwt['jwt_private_key_path'], $this->jwt['jwt_public_key_path'], $this->jwt['jwt_key_pass_phrase']);
    }

    public function encode($userId, $username)
    {
        $tokenId = base64_encode(uniqid('jwt-token', true));
        $issuedAt = time();
        $notBefore = $issuedAt;             //Adding 10 seconds
        $expire = $notBefore + $this->jwt['jwt_token_ttl'];            // Adding 60 seconds
        $issuer = $_SERVER['SERVER_NAME'];
        /*
         * Create the token as an array
         */
        $data = [
            'iat' => $issuedAt,         // Issued at: time when the token was generated
            'jti' => $tokenId,          // Json Token Id: an unique identifier for the token
            'nbf' => $notBefore,        // Not before
            "iss" => $issuer,           // Issuer
            'exp' => $expire,           // Expire
            'data' => [                 // Data related to the signer user
                'userId' => $userId,    // userid from the users table
                'userName' => $username,// User name
            ]
        ];

        $key = $this->keyLoader->dumpKey();

        return FJTW::encode($data, $key, $this->alg);
    }

    public function isValid($token): bool
    {
        $key = $this->keyLoader->dumpKey();
        $token = trim(str_replace('Bearer ', '',$token));
        try {
            FJTW::decode($token, $key, [$this->alg]);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}