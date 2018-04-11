<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

$dotenv = new Dotenv\Dotenv(__DIR__ . "/../");
$dotenv->load();

// cors
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);

    /* if client origin exists in allowed origins, set the allowed origin
        to the client origin to allow CORS */ 
    $allowed_origins = explode(',', getenv("ALLOWED_ORIGINS"));
    $client_origin = array_key_exists('HTTP_ORIGIN', $_SERVER) ?  
        $_SERVER['HTTP_ORIGIN']  : null;

    $allowed_origin = $client_origin && in_array($client_origin, $allowed_origins) ?
        $client_origin : null;
   
    return $response
        ->withHeader('Access-Control-Allow-Origin', $allowed_origin)
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Set up dependencies 
require __DIR__ . '/../src/dependencies.php';

// Register routes
require __DIR__ . '/../src/routes/app.php';


$app->run();