<?php

namespace App\Repos;

class UserRepo 
{
  public function __construct (\Slim\Container $container) {
    $this->container = $container;
  }

  /**
   * get user 
   */
  public function get_user($id) {
    $stmt = $this->container->db
      ->prepare(
        "SELECT id, name, email, account_type as role, verified, postcode, 
          range as radius, has_avatar
        FROM users WHERE id = ?"
      );
    
    try {
      $stmt->execute([$id]);
    } catch (Exception $e) {
      return false;
    }

    return $stmt->fetch();
  }

  /**
   * save user verification credentials in the DB
   */
  public function save_verification_credentials($credentials) {
    $stmt = $this->container->db->prepare(
      "INSERT INTO users_verification_credentials
        (user_id, adi_no, license_since_month, license_since_year, adi_license_src)
        VALUES (?,?,?,?,?)"
    );

    try {
      $stmt->execute([
        $credentials->user_id,
        $credentials->adi_no,
        $credentials->license_since_month,
        $credentials->license_since_year,
        ''
      ]);
    } catch (Exception $e) {
      return ['success' => false, 'message' => $e];
    }

    return [
      'success' => true, 
      'message' => 'user verification credentials saved'
    ];
  }


  /**
   * get super admin with the specified email
   */
  public function get_super_admin_where_email($username) {
    $stmt = $this->container->db
      ->prepare(
        "SELECT id, name, email, password, account_type, verified
        FROM users 
        WHERE email = ? 
          AND account_type = 222 
        LIMIT 1"
      );
    
    try {
      $stmt->execute(array($username));
      $super_admin = $stmt->fetch();
    } catch (PDOException $e) {
      return ['success' => false, 'message' => $e];
    }

    return [
      'success' => true, 
      'message' => 'found user',
      'user' => $super_admin
    ];
  }

  /**
   * get all users verfications details of whom are waiting to be verified
   */
  public function get_users_verification_credentials() {
    $stmt = $this->container->db
      ->prepare(
        "SELECT users.id, users.name, users.email, 
          uvc.adi_no, uvc.license_since_month, uvc.license_since_year
        FROM users 
        INNER JOIN  users_verification_credentials as uvc
          ON uvc.user_id = users.id
        WHERE NOT users.verified = 1"
      );
    
      try {
        $stmt->execute();
        $users_verification_credentials = $stmt->fetchAll();
      } catch (PDOException $e) {
        return 500;
      }

     return  $users_verification_credentials;
  }

  /**
   * update user verification status 
   */
  public function update_instructor_verification($id, $new_status) {
    $stmt = $this->container->db 
      ->prepare("UPDATE users SET verified = ? WHERE id  = ?");

    try {
      $stmt->execute([$new_status, $id]);
    } catch (PDOException $e) {
      return false;
    }

    return 'user verification status updated';
  }

  /**
   * update instructor longitude, latitude and radius
   */
  public function update_instructor_coverage($id, $new_coverage) {
    $stmt = $this->container->db
      ->prepare(
        "UPDATE users SET distance_longitude = ?, distance_latitude = ?, 
          range = ?, postcode = ?
          WHERE id = ?"
      );
    
      try {
        $stmt->execute([
          $new_coverage->longitude, 
          $new_coverage->latitude, 
          $new_coverage->radius, 
          $new_coverage->postcode,
          $id
        ]);
      } catch (PDOException $e) {
        return false;
      }

      return 'user coverage updated';
  }

  /**
   * 
   */
  public function update_instructor_has_avatar($id, $status) {
    $stmt = $this->container->db 
      ->prepare("UPDATE users SET has_avatar = ? WHERE id = ?");
    
    try {
      $stmt->execute([$status, $id]);
    } catch (PDOException $e) {
      return 500;
    }

    return 'instructor avatar updated';
  }
}