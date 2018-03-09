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
   */
  public function get_review_by_token($token) {
    $stmt = $this->container->db->prepare(
      "SELECT r.instructor_id, r.name, r.email, CONCAT(i.first_name, ' ', i.surname) AS instructor_name
      FROM review_invite_tokens AS r
      INNER JOIN instructors AS i
        ON i.id = r.instructor_id
      WHERE token = ?"
    );

    $stmt->execute([$token]);

    return $stmt->fetch();
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
        VALUES (?,?,?,?)"
    );

    $token = $this->service->generate_random_token(22);
    $stmt->execute([$instructor_id, $invite->name, $invite->email, $token]);
  }


  /**
   * get instructor's review requests 
   */
  public function get_review_requests($id) {
    $stmt = $this->container->db->prepare(
      "SELECT id, name, email FROM review_invite_tokens WHERE instructor_id = ?"
    );

    $stmt->execute([$id]);
    return $stmt->fetchAll();
  }
}