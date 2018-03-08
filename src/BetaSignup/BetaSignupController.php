<?php


namespace App\BetaSignup;

use App\BetaSignup\BetaSignupService;
use App\BetaSignup\BetaSignupRepo;

class BetaSignupController 
{
  public function __construct(\Slim\Container $container) {
    $this->service = new BetaSignupService();
    $this->repo = new BetaSignupRepo($container); 
  }

  /**
   * Store email in the DB so user can be emailed upon Beta Launch
   */
  public function signup($request, $response, $args) {
    $email = $request->getParam('email');

    if ($val = $this->service->validate_signup($email)) {
      return $response->withJson($val, 422);
    }

    $save_signup = $this->repo->store_beta_signup($email);

    if ($save_signup === 500) {
      return $response->withJson('internal server error', 500);
    }

    return $response->withJson('signed up to beta launch');
  }
}

