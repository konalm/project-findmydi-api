<?php 

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Hmac\Sha256;

use src\Services\TokenService;


class AuthController
{
  public function __construct (Slim\Container $container) {
    $this->container = $container;
  }

  /**
   * If email is found in DB and matches password user inputed then 
   * return a JWT access token so client can access API
   */
  public function login($request, $response, $args) {
    $email = trim($request->getParam('email'));
    $password = trim($request->getParam('password'));

    try {
      $sth = $this->container->db
          ->prepare(
            "SELECT id, name, email, password 
            FROM users WHERE email = ? LIMIT 1"
          );

      $sth->execute(array($email));
      $user = $sth->fetch();

    } catch (PDOException $e) {
      return $response->withJson('issue with login, internal server error', 500);
    }

    if (!$user) {
      return $response->withJson('Incorrect Login Details', 401);
    }

    if (!password_verify($password, $user['password'])) {
      return $response->withJson('Incorrect Login Details', 401);
    }

    $token_service = new TokenService();
    $token = $token_service>create_jwt_token($user);

    return $response->withJson([
      'message' => 'Authorization Granted',
      'access_token' => $token
    ]);
  }


  /**
   * 
   */
  private function create_jwt_token($user) {
    $signer = new Sha256();

    $token = (new Builder())
      ->setIssuer('findmydrivinginstructor') // Configures the issuer (iss claim)
      ->setAudience('http://instructor.io') // Configures the audience (aud claim)
      ->setId('an10ggam10q', true) // Configures the id (jti claim), replicating as a header item
      ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
      ->setNotBefore(time()) // Configures the time that the token can be used (nbf claim)
      ->setExpiration(time() + 3600) // Configures the expiration time of the token (exp claim)
      ->set('uid', 1) // Configures a new claim, called "uid"
      ->set('user', $user)
      ->sign($signer, 'secret')
      ->getToken(); // Retrieves the generated token

      return (string)$token;
  }


  /**
   * 
   */
  public function verify_jwt_token($request, $response, $args) {
    $signer = new Sha256();
    $token = (new Parser())->parse((string) $request->getParam('token')); 

    $data = new ValidationData();
    $data->setIssuer('findmydrivinginstructor');
    $data->setAudience('http://instructor.io');
    $data->setId('an10ggam10q');
    $data->setCurrentTime(time());

    if (!$token->validate($data)) {
      return $response->withJson('token not valid', 406);
    }

    if (!$token->verify($signer, 'secret')) {
      return $response->withJson('token not verified', 406);
    }
    
    return $response->withJson('authorization granted', 200);
  }
}