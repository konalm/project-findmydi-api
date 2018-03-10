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
   * resend email review invitation to recipient
   */
  public function resend_review_invite($request, $response, $args) {
    $invite_id = $args['invite_id'];
    $instructor_id = $this->token_service->get_decoded_user($request)->id;
    
    $review_invite = $this->repo->get_review_invite($invite_id, $instructor_id);

    if (!$review_invite) {
      return $response->withJson('revew invite does not exist', 403);
    }

    $subject = 'Driving Instructor Review Invitation';
  
    $body = $this->service
      ->build_email_body($review_invite['name'], $review_invite['token']);

    $this->mail_service
      ->send_email($subject, $body, $review_invite['email'], $review_invite['name']);
    
    // $this->mail_service
    //   ->send_email($subject, $body, 'connor@codegood.co', $review_invite['name']);

    return $response->withJson('review invitation has been re-sent');
  }


  /**
   * cancel review invitation by deleting the review invite model 
   */
  public function cancel_review_invite($request, $response, $args) {
    $invite_id = $args['invite_id'];
    $instructor_id = $this->token_service->get_decoded_user($request)->id;

    if (!$this->repo->get_review_invite($invite_id, $instructor_id)) {
      return $response->withJson('review invite does not exist', 422);
    }

    $this->repo->destroy_review_invite_token($instructor_id, $invite_id);
    return $response->withJson('review invitation has been cancelled');
  }


  /**
   * get review using token
   */
  public function get_review_by_token($request, $response, $args) {
    $token = $args['token'];

    if (!$token) {
      return $response->withJson('token is required', 403);
    }

    $review = $this->repo->get_review_by_token($token);
    return $response->withJson($review);
  }


  /**
   * store request request in DB
   */
  public function save_review_request($request, $response, $args) {
    $review_request = new \stdClass();
    $review_request->name = $request->getParam('name');
    $review_request->email = $request->getParam('email');
    $review_request->postcode = $request->getParam('postcode');
    $review_request->instructor_name = $request->getParam('instructorName');
    $review_request->review_message = $request->getParam('reviewMessage');
    $review_request->rating = $request->getParam('rating');

    if ($val = $this->service->validate_review_request($review_request)) {
      return $response->withJson($val, 422);
    }
    
    $saved_review_request_model = $this->repo->save_review_request($review_request);

    return $response->withJson([
      'message' => 'new review request saved',
      'data' => $saved_review_request_model
    ]);
  }


  /**
   * validate client entered necassery data 
   * get required data from the review token 
   * save review
   * destroy review token invite (so it can't be reused)
   */
  public function save_review($request, $response, $args) {
    $review = new \stdClass();
    $review->token = $request->getParam('token');
    $review->review_message = $request->getParam('reviewMessage');
    $review->rating = $request->getParam('rating');

    if ($val = $this->service->validate_review($review)) {
      return $response->withJson($val, 422);
    }

    $review_token_data = $this->repo->get_review_by_token($review->token);
    $review->instructor_id = $review_token_data['instructor_id'];
    $review->reviewer_name = $review_token_data['name'];
    $review->reviewer_email = $review_token_data['email'];
    
    if (!$review_token_data) {
      return $response->withJson('review token does not exist', 403);
    }

    $saved_review_model = $this->repo->save_review($review);
    $this->repo->destroy_invite_token($review->token);

    return $response->withJson([
      'message' => 'new review saved',
      'data' => $saved_review_model
    ]);
  }


  /**
   * verify review token exists and is legit
   */
  public function verify_review_token($request, $response, $args) {
    $token = $args['token'];
    $verify = $this->repo->verify_token_exists($token);

    return $response->withJson($verify);
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

    return $response->withJson($reviews);
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

    $saved_model = $this->repo->save_review_invite_token($user->id, $invite);

    $subject = 'Driving Instructor Review Invitation';
    $body = $this->service->build_email_body($user->name, $saved_model['token']);

    $this->mail_service->send_email($subject, $body, $invite->email, $invite->name);

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