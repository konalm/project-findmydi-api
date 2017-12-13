<?php

use Slim\Http\Request;
use Slim\Http\Response;

use Src\Services\TokenService;


$controller_path = __DIR__ . '/../src/controllers/';

require $controller_path . 'UserController.php';
require $controller_path . 'SearchController.php';
require $controller_path . 'AuthController.php';

// require __DIR__ . '/../src/services/TokenService.php';


$app->post('/users', '\UserController:save_user');

$app->get('/search-instructors/{postcode}', '\SearchController:search_instructors');

$app->post('/login', '\AuthController:login');
 
$app->get('/jwt', '\AuthController:create_jwt_token');
$app->post('/jwt-verify', '\AuthController:verify_jwt_token');

$app->get('/secret', function ($request, $response, $args) {
    $token_service = new TokenService();

    if (!$token_service->verify_token($request)) {
        return $response->withJson('Not Authorized', 406);
    }
;

    return $response->withJson('reached secret endpoint', 200);
});