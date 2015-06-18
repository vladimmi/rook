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

$modelManager = $di->get('orm_model_manager');

//direct query, just get raw data array without any helpers
$result1 = [];
$response = $modelManager->query('SELECT * FROM hub.characters WHERE name = $1', ['name' => 'Vladimmi Hakaari']);
if(pg_num_rows($response)) {
    while(($r = pg_fetch_assoc($response)) !== false) {
        $result1[] = $r;
    }
}

//fetch records via ORM helpers
$result2 = \Rook\Model\Character::fetchByName('Vladimmi Hakaari');

//lets compare...
echo "Raw results:\r\n";
var_dump($result1);
echo "Hydrated results:\r\n";
var_dump($result2);