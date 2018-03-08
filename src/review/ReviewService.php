<?php 

namespace App\Review;

use Twig_Loader_FileSystem;
use Twig_Environment;



class ReviewService
{
  /**
   * validate data for review invitation
   */
  public function validate_review_invite($email_data) {
    if (!$email_data->email) {
      return 'email is required';
    }

    if (!$email_data->name) {
      return 'name is required';
    }
  }

  /**
   * 
   */
  public function build_email_body($instructor_name) {
    $loader = new Twig_Loader_Filesystem(__DIR__ .'/resources/mail-templates');

    $twig = new Twig_Environment($loader, array());

    $instructor_name = 'John Doe';
    return $twig->render('reviewInvite.html', array('from' => $instructor_name));
  }

  /**
   * generate random string (usually for email invite token)
   */
  function generate_random_token($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
  }
}