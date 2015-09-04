<?php

use org\bovigo\vfs\vfsStream;

error_reporting(E_ALL | E_STRICT);

// Ensure that composer has installed all dependencies
if (!file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'composer.lock')) {
    die("Dependencies must be installed using composer:\n\ncomposer.phar install\n\n"
        . "See https://github.com/composer/composer/blob/master/README.md for help with installing composer\n");
}

// Include the composer autoloader
$loader = require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

define('MOCK_BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'mock');
define('FIXTURES_BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'fixtures');

$root = vfsStream::setup('root');

$file = vfsStream::newFile('cert.pem');
$root->addChild($file);

$file = vfsStream::newFile('not-readable.pem', '0000');
$root->addChild($file);

