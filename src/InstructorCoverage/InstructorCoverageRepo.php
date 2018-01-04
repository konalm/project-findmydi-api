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
}