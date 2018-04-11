<?php 

namespace App\SuperAdmin;

use \Interop\Container\ContainerInterface as ContainerInterface;
use App\Services\TokenService;


class SuperAdminController 
{
  protected $container; 

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
    $this->repo = new SuperAdminRepo($container); 
    $this->token_service = new TokenService($container);
  }

  /**
   * check if authorized using JWT token
   */
  public function auth($request, $response, $args) {
    if (!$this->token_service->verify_super_admin_token($request)) {
      return $response->withJson('Not Authenticated', 401); 
    }

    $super_admin = $this->token_service->get_decoded_user($request);

    if (!$super_admin) {
      return $response->withJson('Not Authorized', 406); 
    }

    return $response->withJson($super_admin, 200);
  }
}