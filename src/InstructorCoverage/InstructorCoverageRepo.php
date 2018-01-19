<?php 

namespace App\InstructorCoverage;

class InstructorCoverageRepo
{
  public function __construct (\Slim\Container $container) {
    $this->container = $container;
  }

  /**
   * save instructor coverage model
   */
  public function save($instructor_coverage) {
    $stmt = $this->container->db->prepare(
      'INSERT INTO instructor_coverage
        (user_id, postcode, longitude, latitude, range)
        VALUES (?,?,?,?,?)'
    );

    try {
      $stmt->execute([
        $instructor_coverage->user_id,
        $instructor_coverage->postcode,
        $instructor_coverage->longitude,
        $instructor_coverage->latitude,
        $instructor_coverage->range,
      ]);
    } catch (Exception $e) {
      return 500;
    }

    return 'instructor coverage saved';
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

}