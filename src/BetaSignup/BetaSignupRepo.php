<?php 

namespace App\BetaSignup;

class BetaSignupRepo
{
  public function __construct(\Slim\Container $container) {
    $this->container = $container; 
  }

  /**
   * store beta sign up
   */
  public function store_beta_signup($email) {
    $stmt = $this->container->db->prepare(
      'INSERT INTO beta_signups (email, signup_date) VALUES (?, NOW())'
    );

    try {
      $stmt->execute([$email]);
    } catch (PDOException $e) {
      return 500;
    }
  }
}