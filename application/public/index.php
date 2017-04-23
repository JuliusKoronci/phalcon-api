<?php

if (true !== defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__));
}

require_once APP_PATH . '/etc/Bootstrap.php';

/**
 * We don't want a global scope variable for this
 */
(new Bootstrap())->run();

