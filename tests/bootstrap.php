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