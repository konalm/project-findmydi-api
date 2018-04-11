<?php 

namespace App\Stats;

class StatsRepo
{
  public function __construct(\Slim\Container $container) {
    $this->container = $container;
  }

  /**
   * 
   */
  public function save_stat ($stat) {
      $stmt = $this->container->db->prepare(
        "INSERT INTO statistics
         (instructor_id, event, date)
         VALUES(?, ?, NOW())
         RETURNING id, instructor_id, event, date"
      );

      $stmt->execute([$stat->instructor_id, $stat->event]);
      return $stmt->fetch();
  }


  /**
   * get all statistics for instructor 
   */
  public function get_stats ($user_id) {
    $stmt = $this->container->db->prepare(
      "SELECT COUNT(id) filter (WHERE event = 'email clicked') as email_click_count,
        COUNT(id) filter (WHERE event = 'contact number clicked') as number_click_count
      FROM statistics 
      WHERE instructor_id = ?"
    );

    $stmt->execute([$user_id]);
    return $stmt->fetch();
  }
}