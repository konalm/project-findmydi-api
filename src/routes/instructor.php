<?php

use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/../middleware/instAuth.php';


$app->get('/instructor', 'InstructorController:get_instructor');
$app->post('/instructors', 'InstructorController:save');

$app->put('/instructors-profile', 'InstructorController:update_profile');
$app->post('/update-avatar', 'InstructorController:update_avatar');

$app->post('/instructor-adi-licence-upload', 
  'InstructorController:upload_adi_licence_for_review');

  
/**
 * coverages
 */
$app->get('/instructor-coverages', 'InstructorCoverageController:get_instructor_coverages')
  ->add($inst_auth);
  
$app->post('/instructors-coverage', 'InstructorCoverageController:save');

$app->post('/instructor-region-coverages', 'InstructorCoverageController:save_region');
$app->put('/instructor-region-coverages/{id}', 'InstructorCoverageController:update_region');

$app->put('/instructors-coverage/{id}', 'InstructorCoverageController:update');
$app->delete('/instructors-coverage/{id}', 'InstructorCoverageController:delete');


$app->get('/instructors-in-review', 'InstructorController:get_instructors_in_review');

$app->put('/instructor-adi-licence-status/{id}', 
  'InstructorController:update_adi_licence_status');

$app->get('/search-instructors/{postcode}', 'SearchController:search_instructors');

$app->get('/check', 'InstructorController:check_verified');


/**
 * induction 
 */
$app->get('/instructor-induction-info', 
  'InstructorController:get_instructor_induction_info')
  ->add($inst_auth);

$app->put('/instructor-induction-intro-read', 
  'InstructorController:update_induction_intro_read')
  ->add($inst_auth);

$app->put('/instructor-hourly-rate', 'InstructorController:update_instructor_hourly_rate')
  ->add($inst_auth);