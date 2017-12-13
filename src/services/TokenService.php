<?php

namespace Src\Services;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class TokenService 
{
  /**
   * create a jwt token and sign with the api secret
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
      ->sign($signer, getenv('APP_SECRET'))
      ->getToken(); // Retrieves the generated token

      return (string)$token;
  }


  /**
   * verify token is validated and verified
   */
  public function verify_token($request) {
    error_log('verify token');

    $signer = new Sha256();
    $token = implode("", $request->getHeader('Authorization'));

    if (!$token) { error_log("no token"); return false; }

    try {
      $token = (new Parser())->parse((string) $token);
    } catch (Exception $e) {
      error_log('couldnt parse token');
      return false;
    }

    $data = new ValidationData();
    $data->setIssuer('findmydrivinginstructor');
    $data->setAudience('http://instructor.io');
    $data->setId('an10ggam10q');
    $data->setCurrentTime(time());

    if (!$token->validate($data)) { return false; }

    if (!$token->verify($signer, getenv('APP_SECRET'))) {
      return false;
    }

    return true;
  }
}