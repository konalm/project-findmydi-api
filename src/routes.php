<?php

use Slim\Http\Request;
use Slim\Http\Response;

use App\Services\TokenService;


$controller_path = __DIR__ . '/../src/controllers/';

require $controller_path . 'SearchController.php';


$app->get('/user', 'UserController:get_user');
$app->get('/user-db', 'UserController:get_user_from_db');
$app->post('/users', 'UserController:save_user');

$app->put('/instructor-verification/{id}', 'UserController:update_instructor_verification');

$app->post('/verification-details', 'UserController:save_verification_details');
$app->post('/upload-avatar', 'UserController:upload_avatar');

$app->put('/instructor-coverage', 'UserController:update_instructor_coverage');

$app->get('/search-instructors/{postcode}', '\SearchController:search_instructors');

$app->post('/login', 'AuthController:login');

$app->get('/super-admin', 'UserController:get_super_admin');
$app->post('/super-admin-login', 'AuthController:super_admin_login');

$app->get('/users-verification-credentials', 
  'UserController:get_users_verification_credentials');

$app->get('/jwt', 'AuthController:create_jwt_token');
$app->post('/jwt-verify', 'AuthController:verify_jwt_token');


$app->get('/img/adi-license/{user_id}', 'ImgController:get_adi_licence_photo');
$app->get('/img/avatar/{user_id}', 'ImgController:get_instructor_avatar');