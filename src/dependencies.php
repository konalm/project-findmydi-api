<?php 

use App\Instructor\InstructorController;
use App\InstructorCoverage\InstructorCoverageController;
use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\ImgController;

$container = $app->getContainer();

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

// db connection
$container['db'] = function ($c) {
    $db = $c['settings']['db'];

    $pdo = new PDO("pgsql:host=" . $db['host'] . ";port=" . $db['port'] . 
        ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
   
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
};

/**
 * get the directory of upload dir in settings
 */
$container['getUploadDir'] = function ($c) {
    return $c['settings']['uploadDir'];
};


/**
 * inject controllers
 */
$container['InstructorController'] = function ($c) {
  return new InstructorController($c);
};
$container['InstructorCoverageController'] = function ($c) {
    return new InstructorCoverageController($c);
  };

$container['UserController'] = function($c) {
  return new UserController($c);
};

$container['AuthController'] = function($c) {
  return new AuthController($c);
};

$container['ImgController'] = function($c) {
    return new ImgController($c);
};

