<?php 

namespace App\Services;

class AuthService 
{
  /**
   * 
   */
  public function validate_login_details($login_details) {
    if (!$login_details->email) {
      return 'email is required';
    }

    if (!$login_details->password) {
      return 'password is required';
    }

    return false;
  }
}