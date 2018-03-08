<?php 

namespace App\BetaSignup;

class BetaSignupService 
{
  /**
   * validate new beta sign up
   */
  public function validate_signup($email) {
    if (!$email) { 
      return 'email is required';
    }
  }
}