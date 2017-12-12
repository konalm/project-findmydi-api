<?php

use Slim\Http\Request;
use Slim\Http\Response;

$controller_path = __DIR__ . '/../src/controllers/';

require $controller_path . 'UserController.php';
require $controller_path . 'SearchController.php';
require $controller_path . 'AuthController.php';


$app->post('/users', '\UserController:save_user');

$app->get('/search-instructors/{postcode}', '\SearchController:search_instructors');

$app->post('/login', '\AuthController:login');

$app->get('/jwt', '\AuthController:create_jwt_token');
$app->post('/jwt-verify', '\AuthController:verify_jwt_token');