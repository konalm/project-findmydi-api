<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/instructor', 'InstructorController:get_instructor');
$app->post('/instructors', 'InstructorController:save');

$app->put('/instructors-profile', 'InstructorController:update_profile');

$app->post('/instructors-coverage', 'InstructorCoverageController:save');
$app->put('/instructors-coverage/{id}', 'InstructorCoverageController:update');
$app->delete('/instructors-coverage/{id}', 'InstructorCoverageController:delete');

