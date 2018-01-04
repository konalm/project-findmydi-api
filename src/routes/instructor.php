<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->post('/instructors', 'InstructorController:save');

$app->post('/instructors-coverage', 'InstructorCoverageController:save');