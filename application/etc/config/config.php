<?php

return [
    'app' => [
        'version' => '0.0.1',
        'timezone' => getenv('APP_TIMEZONE'),
        'debug' => getenv('APP_DEBUG'),
        'env' => getenv('APP_ENV'),
        'url' => getenv('APP_URL'),
        'name' => getenv('APP_NAME'),
        'project' => getenv('APP_PROJECT'),
        'description' => getenv('APP_DESCRIPTION'),
        'keywords' => getenv('APP_KEYWORDS'),
        'repo' => getenv('APP_REPO'),
        'docs' => getenv('APP_DOCS'),
        'baseUri' => getenv('APP_BASE_URI'),
        'staticUrl' => getenv('APP_STATIC_URL'),
        'lang' => getenv('APP_LANG'),
        'supportEmail' => getenv('APP_SUPPORT_EMAIL'),
    ],
    'cache' => [
        'driver' => getenv('CACHE_DRIVER'),
        'viewDriver' => getenv('VIEW_CACHE_DRIVER'),
        'prefix' => getenv('CACHE_PREFIX'),
        'lifetime' => getenv('CACHE_LIFETIME'),
    ],
    'swagger' => [
        'path' => APP_PATH . '/src',
        'host' => getenv('SWAGGER_HOST'),
        'schemes' => explode(',', getenv('SWAGGER_SCHEMES')),
        'basePath' => getenv('SWAGGER_BASEPATH'),
        'version' => getenv('SWAGGER_VERSION'),
        'title' => getenv('SWAGGER_TITLE'),
        'description' => getenv('SWAGGER_DESCRIPTION'),
        'email' => getenv('SWAGGER_EMAIL'),
        'jsonUri' => getenv('SWAGGER_JSON_URI'),
    ],
    'memcached' => [
        'host' => getenv('MEMCACHED_HOST'),
        'port' => getenv('MEMCACHED_PORT'),
        'weight' => getenv('MEMCACHED_WEIGHT'),
    ],
    'logger' => [
        'path' => APP_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs',
    ],
    'google' => [
        'analytics' => getenv('GOOGLE_ANALYTICS'),
    ],
    'routes' => require APP_PATH . '/etc/config/routes.php',
    'plugins' => require APP_PATH . '/etc/config/plugins.php',
    'firewall' => require APP_PATH . '/etc/config/firewall.php',
    'jwt' => require APP_PATH . '/etc/config/jwt.php',
];
