<?php 

namespace App\InstructorCoverage;


class InstructorCoverageRepo
{
  public function __construct (\Slim\Container $container) {
    $this->container = $container;
  }

  /**
   * get all coverages for instructor 
   */
  public function get_coverages($id) {
    $stmt = $this->container->db->prepare(
      'SELECT id, postcode, region, range, coverage_type, longitude, latitude
      FROM instructor_coverage
      WHERE user_id = ?'
    );
    
    try {
      $stmt->execute([$id]);
    } catch (Exception $e) {
      return 500;
    }

    return $stmt->fetchAll();
  }

  /**
   * save postcode instructor coverage model
   */
  public function save_postcode($instructor_coverage) {
    $stmt = $this->container->db->prepare(
      'INSERT INTO instructor_coverage
        (user_id, postcode, longitude, latitude, range, coverage_type)
        VALUES (?,?,?,?,?,?)'
    );

    try {
      $stmt->execute([
        $instructor_coverage->user_id,
        $instructor_coverage->postcode,
        $instructor_coverage->longitude,
        $instructor_coverage->latitude,
        $instructor_coverage->range,
        'postcode'
      ]);
    } catch (Exception $e) {
      return 500;
    }

    return 'instructor postcode coverage saved';
  }

  
  /**
   * save region instructor coverage model 
   */
  public function save_region($coverage) {
    $stmt = $this->container->db->prepare(
      'INSERT INTO instructor_coverage
        (user_id, region, longitude, latitude, range, coverage_type)
        VALUES (?,?,?,?,?,?)'
    );

    try {
      $stmt->execute([
        $coverage->user_id,
        $coverage->region,
        $coverage->long,
        $coverage->lat,
        $coverage->range,
        'region'
      ]);
    } catch (Exeption $e) {
      return 500;
    }

    return 'instructor region coverage saved';
  }


  /**
   * update instructor coverage model 
   */
  public function update($instructor_coverage) {
    $stmt = $this->container->db->prepare(
      'UPDATE instructor_coverage 
        SET postcode = ?, longitude = ?, latitude = ?, range = ?
        WHERE id = ?'
    );

    try {
      $stmt->execute([
        $instructor_coverage->postcode,
        $instructor_coverage->longitude,
        $instructor_coverage->latitude,
        $instructor_coverage->range,
        $instructor_coverage->id
      ]);
    } catch (Exception $e) {
      return false;
    }

    return true;
  }

  /**
   * update region coverage model 
   */
  public function update_region($coverage) {
    $stmt = $this->container->db->prepare(
      'UPDATE instructor_coverage 
        SET region = ?, longitude = ?, latitude = ?, range = ?
        WHERE id = ?'
    );

    try {
      $stmt->execute([
        $coverage->region,
        $coverage->long,
        $coverage->lat,
        $coverage->range,
        $coverage->id
      ]);
    } catch (Exception $e) {
      return 500;
    }
  }


  /**
   * delete instructor coverage model
   */
  public function delete($id) {
    $stmt = $this->container->db->prepare(
      'DELETE FROM instructor_coverage WHERE id = ?'
    );

    try {
      $stmt->execute([$id]);
    } catch (Exception $e) {
      return false;
    }
  }


  /**
   * check instructor is authorized access to instructor coverage model
   */
  public function check_instructor_authorized($user_id, $instructor_coverage_id) {
    $stmt = $this->container->db->prepare(
      'SELECT user_id FROM instructor_coverage WHERE id = ?'
    );

    try {
      $stmt->execute([$instructor_coverage_id]);
    } catch (Exception $e) {
      return false;
    }

    return $user_id === $stmt->fetch()['user_id'];
  } 


  /**
   * 
   */
  public function get_coverages_for_verified_instructors() {
    $stmt = $this->container->db->prepare(
      "SELECT instructors.id, first_name, surname, email, contact_number, gender, 
        hourly_rate, avatar_url, offer, ic.postcode, ic.longitude, ic.latitude, 
        ic.range, COUNT(reviews.id) AS review_count, AVG(reviews.rating) AS review_rating 
      FROM instructor_coverage AS ic
      INNER JOIN instructors 
        ON instructors.id = ic.user_id
      LEFT JOIN reviews
        ON reviews.instructor_id = instructors.id
      WHERE instructors.verified = true
      GROUP BY instructors.id, first_name, surname, email, contact_number, gender,
        hourly_rate, avatar_url, offer, ic.postcode, ic.longitude, ic.latitude,
        ic.range"
    );

    try {
      $stmt->execute();
    } catch (PDOException $e) {
      return 500;
    }

    return $stmt->fetchAll();
  }
}