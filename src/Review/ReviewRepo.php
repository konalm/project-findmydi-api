<?php 

namespace App\Review;

use App\Review\ReviewService; 


class ReviewRepo
{
  public function __construct(\Slim\Container $container) {
    $this->container = $container;
    $this->service = new ReviewService();
  }

  /**
   * sent invite to used (so it can't be reused for another review)
   */
  public function invite_token_used ($token) {
    $stmt = $this->container->db->prepare(
      "UPDATE review_invite_tokens SET used = true WHERE token = ?"
    );

    $stmt->execute([$token]);
  }

  /**
   * check if instructor has review or review request with a given email already
   */
  public function check_email_prev_used($email, $instructor_id) {
    $stmt = $this->container->db->prepare(
      "SELECT invite.id AS invite_id, r.id as review_id
      FROM review_invite_tokens AS invite
      LEFT JOIN reviews AS r
        ON r.instructor_id = invite.instructor_id
        AND r.reviewer_email = invite.email
      WHERE invite.email = ?
        AND invite.instructor_id = ?"
    );

    $stmt->execute([$email, $instructor_id]);
    return $stmt->fetch();
  }

  /**
   * get review request
   */
  public function get_review_invite($invite_id, $instructor_id) {
    $stmt = $this->container->db->prepare(
      "SELECT id, name, email, token 
      FROM review_invite_tokens
      WHERE id = ? 
        AND instructor_id = ?"
    );

    $stmt->execute([$invite_id, $instructor_id]);
    return $stmt->fetch();
  }

  /**
   * destroy review invite token model 
   */
  public function destroy_review_invite_token($instructor_id, $invite_id) {
    $stmt = $this->container->db->prepare(
      'DELETE FROM review_invite_tokens WHERE id = ? AND instructor_id = ?'
    );

    $stmt->execute([$invite_id, $instructor_id]);
  }

  /**
   * save review request model
   */
  public function save_review_request($review_request) {
    $stmt = $this->container->db->prepare(
      'INSERT INTO review_requests
      (name, email, postcode, instructor_name, review_message, rating)
      VALUES (?,?,?,?,?,?)
      RETURNING *'
    );

    $stmt->execute([
      $review_request->name,
      $review_request->email,
      $review_request->postcode,
      $review_request->instructor_name,
      $review_request->review_message,
      $review_request->rating,
    ]);

    return $stmt->fetch();
  }

  /**
   * destroy review invite token
   */
  public function destroy_invite_token($token) {
    $stmt = $this->container->db->prepare(
      'DELETE FROM review_invite_tokens WHERE token = ?'
    );

    $stmt->execute([$token]);
  }


  /**
   * save review model
   */
  public function save_review($review) {
    $stmt = $this->container->db->prepare(
      'INSERT INTO reviews 
        (instructor_id, reviewer_name, reviewer_email, review_message, rating)
        VALUES (?,?,?,?,?)
        RETURNING *'
    );
   
    $stmt->execute([
      $review->instructor_id,
      $review->reviewer_name,
      $review->reviewer_email,
      $review->review_message,
      $review->rating
    ]);

    return $stmt->fetch();
  }


  /**
   * get review identified by token
   * return false if token has already been used
   */
  public function get_review_by_token($token) {
    $stmt = $this->container->db->prepare(
      "SELECT invite.instructor_id, invite.name, invite.email, invite.used,
        CONCAT(inst.first_name, ' ', inst.surname) AS instructor_name
      FROM review_invite_tokens AS invite
      INNER JOIN instructors AS inst
        ON inst.id = invite.instructor_id
      WHERE token = ?"
    );

    $stmt->execute([$token]);
    $fetch = $stmt->fetch();

    /* return false if email for token already used for review */ 
    if ($fetch['used']) { 
      return [
        'data' => false,
        'message' => 'This invite has already been used.',
        'status' => 403
      ];
    }

    /* no review invite found for token */ 
    if (!$fetch) {
      return [
        'data' => false, 
        'message' => 'invite token does not exist.', 
        'status' => 404
      ];
    }

    return ['data' => $fetch, 'message' => 'token does exist'];
  }


  /**
   * verify token exists 
   */
  public function verify_token_exists($token) {
    $stmt = $this->container->db->prepare(
      "SELECT id FROM review_invite_tokens WHERE token = ?"
    );

    $stmt->execute([$token]);

    return sizeof($stmt->fetchAll()) > 0;
  }


  /**
   * get all of instructors reviews
   */
  public function get_instructor_reviews($id) {
    $stmt = $this->container->db->prepare(
      "SELECT reviewer_name, reviewer_email, review_message, rating, timestamp
      FROM reviews
      WHERE instructor_id = ?"
    );

    $stmt->execute([$id]);
    return $stmt->fetchAll();
  }


  /**
   * store new review invite tokens
   */
  public function save_review_invite_token($instructor_id, $invite) {
    $stmt = $this->container->db->prepare(
      "INSERT INTO review_invite_tokens (instructor_id, name, email, token)
        VALUES (?,?,?,?)
        RETURNING *"
    );

    $token = $this->service->generate_random_token(22);
    $stmt->execute([$instructor_id, $invite->name, $invite->email, $token]);
    return $stmt->fetch();
  }


  /**
   * get instructor's review requests 
   */
  public function get_review_requests($id) {
    $stmt = $this->container->db->prepare(
      "SELECT id, name, email 
      FROM review_invite_tokens 
      WHERE instructor_id = ?
        AND used = false"
    );

    $stmt->execute([$id]);
    return $stmt->fetchAll();
  }
}