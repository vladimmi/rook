<?php

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

$autoloader = require_once('../vendor/autoload.php');

//create DI container
$di = new \Rook\DI\Container();

//save autoloader to container
$di->set('autoloader', $autoloader);

//register annotation reader as service
$di->set('annotations', function() use ($autoloader) {
    AnnotationRegistry::registerLoader(array($autoloader, 'loadClass'));
    return new AnnotationReader();
});

//our model manager
$modelManager = new \Rook\ORM\ModelManager('host=/var/run/postgresql port=5432 user=vladimmi password=v1adimm1');

//save it as service
$di->set('model_manager', $modelManager);

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