<?php

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

define('PATH_ROOT', __DIR__ . '/../');
define('PATH_CONFIG', PATH_ROOT . 'config/');
define('PATH_PUBLIC', __DIR__ . '/');
define('PATH_VENDOR', PATH_ROOT . 'vendor/');

//autoloaders
$autoloader = require_once(PATH_VENDOR . 'autoload.php');
AnnotationRegistry::registerLoader(array($autoloader, 'loadClass'));

//create DI container
$di = new \Rook\DI\Container();

//save autoloader to container
$di->set('autoloader', $autoloader);

//load config
$config = new \Rook\Config\Config();
$config->merge(new Rook\Config\Loader\Yaml(PATH_CONFIG . 'base.yml'));

//save config to container
$di->set('config', $config);

//load service definitions from config
$di->setFromConfig();

/** @var \Rook\Model\CharacterRepository $charRepo */
$charRepo = $di->get('repository_character');

//fetch records
$result = $charRepo->find(['name' => 'Vladimmi Hakaari']);

echo "Hydrated results:\r\n";
var_dump($result);