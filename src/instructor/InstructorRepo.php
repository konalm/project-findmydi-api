<?php 

namespace App\Instructor;

class InstructorRepo 
{
  public function __construct (\Slim\Container $container) {
    $this->container = $container; 
  }

  /**
   * get instructor with $id
   */
  public function get($id) {
    $stmt = $this->container->db->prepare(
      "SELECT instructors.id, first_name, surname, email, adi_license_no, 
        gender, verified, hourly_rate, offer, array_to_json(array_agg(c.coverage)) AS coverages
      FROM instructors
      LEFT OUTER JOIN 
      (
        SELECT ic.user_id, json_build_object('postcode', ic.postcode, 'range', ic.range) 
          AS coverage
        FROM instructor_coverage ic
      ) c
        ON instructors.id = c.user_id
      WHERE instructors.id = ?
      GROUP BY instructors.id, first_name, surname, email, adi_license_no, 
        gender, verified, hourly_rate, offer"
    );


    try {
      $stmt->execute([$id]);
    } catch (Exception $e) {
      return false;
    }

    return $stmt->fetch();
  }

  /**
   * save instructor model 
   */
  public function save($new_instructor) {
    $stmt = $this->container->db->prepare(
      'INSERT INTO instructors
        (first_name, surname, email, adi_license_no, gender, verified, password)
      VALUES (?,?,?,?,?,?,?)'
    );

    try {
      $stmt->execute([
        $new_instructor->first_name,
        $new_instructor->surname,
        $new_instructor->email,
        $new_instructor->adi_license_no,
        $new_instructor->gender,
        0,
        password_hash($new_instructor->password, PASSWORD_BCRYPT)
      ]);
    } catch (Exception $e) {
      return 500;
    }

    return 'instructor saved';
  }


  /**
   * check for user with existing email
   */
  public function check_email_exists($email) {
    $stmt = $this->container->db->prepare(
      'SELECT id FROM instructors WHERE email = ?'
    );
    
    try {
      $stmt->execute([$email]);
    } catch (Exception $e) {
      return 500;
    }
 
    return $stmt->fetch();
  }

  /**
   * update instructor profile (usually just hourly rate & offer)
   */
  public function update_profile($id, $new_profile) {
    $stmt = $this->container->db->prepare(
      'UPDATE instructors 
      SET hourly_rate = ?,  offer = ?
      WHERE id = ?'
    );

    try {
      $stmt->execute([
        $new_profile->hourly_rate,
        $new_profile->offer,
        $id
      ]);
    } catch (Exception $e) {
      return 500;
    }

    return 'instructor profile updated';
  }
}