<?php
use rock\base\Alias;

$composerAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    /** @var \Composer\Autoload\ClassLoader $loader */
    $loader = require($composerAutoload);
}

$loader->addPsr4('rockunit\\', __DIR__);

$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = 'site.com';
$_SERVER['REQUEST_URI'] = '/';
date_default_timezone_set('UTC');

define('ROCKUNIT_RUNTIME', __DIR__ . '/runtime');
Alias::setAlias('rockunit', __DIR__);
\rock\base\Alias::setAlias('web', '/assets');