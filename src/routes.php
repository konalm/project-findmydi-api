<?php

use Slim\Http\Request;
use Slim\Http\Response;

$controller_path = __DIR__ . '/../src/controllers/';

require $controller_path . 'UserController.php';



$app->post('/users', '\UserController:save_user');