<?php 

namespace App\Review;

use Twig_Loader_FileSystem;
use Twig_Environment;


class ReviewService
{
  /**
   * validate data for review request from client 
   */
  public function validate_review_request($review_request) {
    if (!$review_request->name) {
      return 'name is required';
    }

    if (!$review_request->email) {
      return 'email is required';
    }

    if (!$review_request->postcode) {
      return 'postcode is required';
    }

    if (!$review_request->instructor_name) {
      return 'instructor name is required';
    }

    if (!$review_request->rating) {
      return 'rating is required';
    }
  }


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
   * validate review
   */
  public function validate_review($review) {
    if (!$review->token) {
      return 'token is required';
    }

    if (!$review->rating) {
      return 'rating is required';
    }
  }

  /**
   * build email with html for review invite
   */
  public function build_email_body($instructor_name, $token) {
    $loader = new Twig_Loader_Filesystem(__DIR__ .'/resources/mail-templates');
    $twig = new Twig_Environment($loader, array());

    error_log('review link -->');
    error_log(getenv('CLIENT_URL') . 'write-review/' . $token);

    return $twig->render('reviewInvite.html', 
      array(
        'from' => $instructor_name,
        'review_link' => getenv('CLIENT_URL') . 'write-review/' . $token
      )
    );
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