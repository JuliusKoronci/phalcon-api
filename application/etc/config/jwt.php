<?php
return [
    'jwt_private_key_path' => APP_PATH . '/var/jwt/private.pem', # ssh private key path
    'jwt_public_key_path' => APP_PATH . '/var/jwt/public.pem',  # ssh public key path
    'jwt_key_pass_phrase' => getenv('JWT_PASS'),                                         # ssh key pass phrase
    'jwt_token_ttl' => getenv('JWT_TTL'),
];