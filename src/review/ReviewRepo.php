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
   * get all of instructors reviews
   */
  public function get_instructor_reviews($id) {
    $stmt = $this->container->db->prepare(
      "SELECT reviewer_name, reviewer_email, review_message, rating
      FROM reviews
      WHERE instructor_id = ?"
    );

    $stmt->execute([$id]);
    return $stmt->fetchAll();
  }

  /**
   * store new review invite tokenx
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