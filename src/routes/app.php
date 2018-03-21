<?php

use Slim\Http\Request;
use Slim\Http\Response;

use App\Services\TokenService;


require __DIR__ . '/../middleware/InstAuth.php';
require __DIR__ . '/instructor.php';
require __DIR__ . '/superAdmin.php';
require __DIR__ . '/../../src/review/ReviewRoutes.php';


$app->get('/test', function ($request, $response, $args) {
  
  return $response->withJson('reached api endpointed');
});


$app->get('/webhook', function ($request, $response, $args) {
  error_log('webhook !!');

  return $response->withJson('webhook triggered !!');
});

$app->get('/user', 'UserController:get_user');
$app->get('/user-db', 'UserController:get_user_from_db');
$app->post('/users', 'UserController:save_user');

$app->put('/instructor-verification/{id}', 
  'UserController:update_instructor_verification');

$app->post('/verification-details', 'UserController:save_verification_details');

$app->put('/instructor-coverage', 'UserController:update_instructor_coverage');

$app->post('/login', 'AuthController:login');

$app->get('/super-admin', 'UserController:get_super_admin');
$app->post('/super-admin-login', 'AuthController:super_admin_login');

$app->get('/users-verification-credentials', 
  'UserController:get_users_verification_credentials');

$app->get('/jwt', 'AuthController:create_jwt_token');
$app->post('/jwt-verify', 'AuthController:verify_jwt_token');


/**
 * Images
 */
$app->get('/img/adi-license/{user_id}', 'ImgController:get_adi_licence_photo');
$app->get('/img/avatar/{user_id}', 'ImgController:get_instructor_avatar');
$app->get('/img[/{path:.*}]', 'ImgController:serve_image');

/**
 * Google Apis
 */
$app->get('/googleapis-autocomplete/{search_term}', 
  'GoogleApisController:get_googleapis_autocomplete_regions');

$app->get('/googleapis-geocode/{address}', 'GoogleApisController:get_googleapis_geocode');

/**
 * postcode 
 */
$app->get('/postcode-lnglat/{postcode}', 'PostcodeController:get_postcode_lnglat');


/**
 * beta 
 */
$app->post('/beta-signup', 'BetaSignupController:signup');
