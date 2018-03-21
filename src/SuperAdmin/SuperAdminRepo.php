<?php 

namespace App\SuperAdmin;

class SuperAdminRepo
{
  public function __construct (\Slim\Container $container) {
    $this->container = $container; 
  }

  /**
   * get super admin 
   */
  public function get($id) {
    $stmt = $this->container->db->prepare(
      "SELECT id, username FROM super_admins WHERE id = ?"
    );

    try {
      $stmt->execute([$id]);
    } catch (Exception $e) {
      return false;
    }

    return $stmt->fetch();
  }

  /**
   * get super admin where (usually for login)
   */
  public function get_where_username($username) {
    $stmt = $this->container->db->prepare(
      "SELECT id, username, password FROM super_admins WHERE username = ?"
    );

    try {
      $stmt->execute([$username]);
    } catch (PDOException $e) {
      return false;
    }

    return $stmt->fetch();
  }
}