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
        gender, verified, hourly_rate, offer, avatar_url, contact_number,
        adi_license_verified, array_to_json(array_agg(c.coverage)) AS coverages
      FROM instructors
      LEFT OUTER JOIN 
      (
        SELECT ic.user_id, json_build_object('id', ic.id, 'postcode', 
          ic.postcode, 'range', ic.range) 
          AS coverage
        FROM instructor_coverage ic
      ) c
        ON instructors.id = c.user_id
      WHERE instructors.id = ?
      GROUP BY instructors.id, first_name, surname, email, adi_license_no, 
        gender, verified, hourly_rate, offer, avatar_url, contact_number, 
        adi_licence_verified"
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
      SET hourly_rate = ?, contact_number = ?, offer = ?
      WHERE id = ?'
    );

    try {
      $stmt->execute([
        $new_profile->hourly_rate,
        $new_profile->contact_number,
        $new_profile->offer,
        $id
      ]);
    } catch (Exception $e) {
      return 500;
    }

    return 'instructor profile updated';
  }


  /**
   * update instructor avatar url 
   */
  public function update_avatar($id, $avatar_url) {
    $stmt = $this->container->db->prepare(
      'UPDATE instructors 
        SET avatar_url = ?
        WHERE id = ?'
    );

    try {
      $stmt->execute([
        $avatar_url,
        $id 
      ]);
    } catch (Exception $e) {
      return 500;
    }
  }


  /**
   * check if adi license model exists 
   */
  public function get_adi_licence($instructor_id) {
    $stmt = $this->container->db->prepare(
      'SELECT id 
      FROM instructor_adi_license_verifications 
      WHERE user_id = ?'
    );

    try {
      $stmt->execute([
        $instructor_id
      ]);
    } catch (Exception $e) {
      return 500; 
    }

    return $stmt->fetch();
  }


  /**
   * update adi license for re-review
   */
  public function update_adi_licence($instructor_id) {
    error_log('update adi license');
    error_log($instructor_id);

    $stmt = $this->container->db->prepare(
      'UPDATE instructor_adi_license_verifications
      SET status = 2
      WHERE user_id = ?'
    );

    try {
      $stmt->execute([$instructor_id]);
    } catch (Exception $e) {
      error_log('CATCH !!');
      return 500;
    }

    error_log('DONE !!');
  }


  /**
   * create adi license for review 
   */
  public function create_adi_licence($instructor_id, $license_src) {
    $stmt = $this->container->db->prepare(
      "INSERT INTO instructor_adi_license_verifications
      (user_id, status, adi_license_src)
      VALUES (?, ?, ?)"
    );

    try {
      $stmt->execute([$instructor_id, 2, $license_src]);
    } catch (Exception $e) {
      return 500;
    }
  }
}