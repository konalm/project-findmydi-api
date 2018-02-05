<?php

namespace App\Services;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class TokenService 
{
  /**
   * create a jwt token and sign with the api secret
   */
  public function create_jwt_token($user, $user_type) {
    $signer = new Sha256();

    $enc_user = [];

    if ($user_type === 'instructor') { 
      $enc_user = $this->build_enc_instructor($user); 
    }

    if ($user_type === 'super_admin') { 
      $enc_user = $this->build_enc_super_admin($user); 
    }

    $token = (new Builder())
      ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
      ->setNotBefore(time()) // Configures the time that the token can be used (nbf claim)
      ->setExpiration(time() + 36000) // Configures the expiration time of the token (exp claim)
      ->set('uid', 1) // Configures a new claim, called "uid"
      ->set('user', $enc_user)
      ->sign($signer, getenv('APP_SECRET'))
      ->getToken(); // Retrieves the generated token

      return (string)$token;
  }


  /**
   * build instructor model for encryption 
   */
  public function build_enc_instructor($user) {
    return [
      'id' => $user['id'],
      'name' => $user['first_name'],
      'email' => $user['email'],
      'verified' => $user['verified']
    ];
  }


  /**
   * build super admin model from encryption
   */
  public function build_enc_super_admin($user) {
    return [
      'id' => $user['id'],
      'username' => $user['username'],
      'access_level' => 1000
    ];
  }


  /**
   * verify token is validated and verified
   */
  public function verify_token($request) {
    $signer = new Sha256();
    $token = implode("", $request->getHeader('Authorization'));

    if (!$token) { return false; }

    try {
      $token = (new Parser())->parse((string) $token);
    } catch (Exception $e) {
      return false;
    }

    $data = new ValidationData();
    $data->setCurrentTime(time());

    if (!$token->validate($data)) { return false; }

    if (!$token->verify($signer, getenv('APP_SECRET'))) {
      return false;
    }

    return true;
  }

  /**
   * verify token is valid and belongs to super admin 
   */
  public function verify_super_admin_token($request) {
    if (!$this->verify_token($request)) {
      return false; 
    }

    $user = $this->get_decoded_user($request); 

    if ($user->access_level !== 1000) {
      return false; 
    }

    return true;
  }

  /**
   * abstract user data from the token payload and decode it
   */
  public function get_decoded_user($request) {
    $token = implode("", $request->getHeader('Authorization'));

    try {
      $token = (new Parser())->parse((string) $token);
    } catch (Exception $e) {
      return false;
    }

    return $token->getClaim('user');
  }
}