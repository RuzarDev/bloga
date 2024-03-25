<?php
// Define constants only if they are not defined
if (!defined('SITE_ROOT')) {
    define('SITE_ROOT', __DIR__);
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://dinamic-site/');
}

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(dirname(__FILE__)));
}
