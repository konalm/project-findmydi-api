<?php 


use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/super-admin-auth', 'SuperAdminController:auth');

