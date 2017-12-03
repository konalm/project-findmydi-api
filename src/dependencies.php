<?php 

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
    
    // $pdo = new PDO("pgsql:host=" . $db['host'] . ";port=" . $db['port'] . 
    //     ";dbname=" . $db["dbname"] . ";user=" . $db["user"] . "password=" . $db["pass"]);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;
};
