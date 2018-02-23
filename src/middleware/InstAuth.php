<?php 

namespace App\Middleware; 

use App\Services\TokenService;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


/**
 * instructor authorization function
 */
$inst_auth = function ($request, $response, $next) {
  $token_service = new TokenService();

  if (!$token_service->verify_token($request)) {
    return $response->withJson('Not Authenticated', 401);
  } 

  return $next($request, $response);
};