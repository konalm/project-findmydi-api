<?php 

namespace App\Controllers;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use \Interop\Container\ContainerInterface as ContainerInterface;

use App\Repos\UserRepo;
use App\SuperAdmin\SuperAdminRepo;
use App\Services\TokenService;
use App\Services\AuthService; 


class AuthController
{
  public function __construct (ContainerInterface $container) {
    $this->container = $container;
    $this->user_repo = new UserRepo($container);
    $this->super_admin_repo = new SuperAdminRepo($container);
    $this->token_service = new TokenService($container);
    $this->service = new AuthService();
  }


  /**
   * If email is found in DB and matches password user inputed then 
   * return a JWT access token so client can access API
   */
  public function login($request, $response, $args) {
    $login_details = new \stdClass();
    $login_details->email = trim($request->getParam('email'));
    $login_details->password = trim($request->getParam('password'));

    $validate_login_details = 
      $this->service->validate_login_details($login_details);
    
    if ($validate_login_details) {
      return $response->withJson($validate_login_details, 422);
    }

    try {
      $sth = $this->container->db->prepare(
            "SELECT id, first_name, surname, email, verified, password
            FROM instructors WHERE email = ? LIMIT 1"
          );

      $sth->execute(array($login_details->email));
      $user = $sth->fetch();
    } catch (PDOException $e) {
      return $response->withJson('issue with login, internal server error', 500);
    }

    if (!$user) {
      return $response->withJson('Incorrect Login Details', 401);
    }

    if (!password_verify($login_details->password, $user['password'])) {
      return $response->withJson('Incorrect Login Details', 401);
    }

    $token_service = new TokenService();
    $token = $token_service->create_jwt_token($user, 'instructor');

    return $response->withJson([
      'message' => 'Authorization Granted',
      'access_token' => $token
    ]);
  }


  /**
   * check username and password of user to confirm they are super admin
   */
  public function super_admin_login($request, $response, $args) {
    $email = trim($request->getParam('username'));
    $password = trim($request->getParam('password'));
    $super_admin = $this->super_admin_repo->get_where_username($email);

    if (!password_verify($password, $super_admin['password'])) {
      return $response->withJson('Not Authorized', 406);
    }

    $token = $this->token_service->create_jwt_token($super_admin, 'super_admin');

    return $response->withJson([
      'message' => 'Authorization Granted',
      'access_token' => $token
    ]);
  }
}

