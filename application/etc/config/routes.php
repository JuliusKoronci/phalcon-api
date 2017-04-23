<?php

return [
    [
        'class' => \Igsem\Docs\Controllers\DocsController::class,
        'methods' => [
            'get' => [
                getenv('SWAGGER_JSON_URI') => 'indexAction',
                '/docs' => 'docsAction',
            ],
        ],
    ],
    [
        'class' => \Application\Front\Controllers\IndexController::class,
        'methods' => [
            'get' => [
                '/' => 'indexAction',
            ],
        ],
    ],
    [
        'class' => \Application\Security\Controllers\LoginController::class,
        'methods' => [
            'post' => [
                '/login' => 'loginAction',
            ],
        ],
    ],
];