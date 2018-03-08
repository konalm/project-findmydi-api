<?php 

namespace App\Review;

use App\Review\ReviewService;
use App\Review\ReviewRepo; 
use App\Services\TokenService;
use App\Services\MailService;


class ReviewController 
{
  public function __construct(\Slim\Container $container) {
    $this->container = $container; 
    $this->repo = new ReviewRepo($container);
    $this->service = new ReviewService();
    $this->token_service = new TokenService();
    $this->mail_service = new MailService();
  }

  /**
   * get all of instructors reviews
   */
  public function get_instructor_reviews($request, $response, $args) {
    $id = $this->token_service->get_decoded_user($request)->id;
 
    try {
      $reviews = $this->repo->get_instructor_reviews($id);
    } catch (Exception $e) {
      return $response->withJson('internal server error', 500);
    }

    return $response->withjson($reviews);
  }


  /**
   * send review invitation via email 
   * store review invitation model
   */
  public function send_review_invite($request, $response, $args) {
    $user = $this->token_service->get_decoded_user($request);
 
    $invite = new \stdClass();
    $invite->email = $request->getParam('email');
    $invite->name = $request->getParam('name');

    if ($val = $this->service->validate_review_invite($invite)) {
      return $response->withJson($val, 422);
    }

    $subject =  'Driving Instructor Review Invitation';
    $body = $this->service->build_email_body($user->name);

    $this->mail_service->send_email($subject, $body, $invite->email, $invite->name);
    $this->repo->save_review_invite_token($user->id, $invite);

    return $response->withJson('review invite has been saved and sent via email');
  }


  /**
   * get instructor's review requests
   */
  public function get_review_requests($request, $response, $args) {
    $id = $this->token_service->get_decoded_user($request)->id;
    $requests = $this->repo->get_review_requests($id);
  
    return $response->withJson($requests);
  }
}