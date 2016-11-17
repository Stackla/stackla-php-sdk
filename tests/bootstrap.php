<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Australia/Sydney');
$autoloaded = @include(__DIR__ . '/../vendor/autoload.php');
if (!$autoloaded && !@include(__DIR__ . '/../../../autoload.php')) {
    die('You must set up the project dependencies, run the following commands:
        wget http://getcomposer.org/composer.phar
        php composer.phar install');
}

use Doctrine\Common\Annotations\AnnotationRegistry;

// Set loader to AnnotationRegistry
AnnotationRegistry::registerLoader(array($autoloaded, 'loadClass'));

define('ACCESS_TOKEN', getenv('ACCESS_TOKEN'));
define('ACCESS_TOKEN_PERMISSION', getenv('ACCESS_TOKEN_PERMISSION') ?: 'rw');
define('API_STACK', getenv('API_STACK'));
define('API_HOST', getenv('API_HOST'));
define('DEFAULT_FILTER_ID', getenv('DEFAULT_FILTER_ID'));
define('DEFAULT_TAG_ID', getenv('DEFAULT_TAG_ID'));
define('STACKLA_POST_TERM_ID', getenv('STACKLA_POST_TERM_ID'));
