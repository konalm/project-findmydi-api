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
        v.status AS adi_licence_verification, v.reject_reason AS adi_licence_reject_reason,
        adi_license_verified, array_to_json(array_agg(c.coverage)) AS coverages
      FROM instructors
      LEFT OUTER JOIN 
      (
        SELECT ic.user_id, 
          json_build_object(
            'id', ic.id, 'postcode', ic.postcode, 'region', ic.region, 
            'range', ic.range, 'coverage_type', ic.coverage_type
          ) AS coverage
        FROM instructor_coverage ic
      ) c
        ON instructors.id = c.user_id
      LEFT JOIN instructor_adi_license_verifications AS v
        ON v.user_id = instructors.id 
      WHERE instructors.id = ?
      GROUP BY instructors.id, first_name, surname, email, adi_license_no, 
        gender, verified, hourly_rate, offer, avatar_url, contact_number, 
        adi_license_verified, v.status, v.reject_reason"
    );

    try {
      $stmt->execute([$id]);
    } catch (PDOException $e) {
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
      SET first_name = ?, surname = ?, email = ?, contact_number = ?, 
        hourly_rate = ?,  offer = ?
      WHERE id = ?'
    );

    try {
      $stmt->execute([
        $new_profile->first_name,
        $new_profile->surname,
        $new_profile->email,
        $new_profile->contact_number,        
        $new_profile->hourly_rate,
        $new_profile->offer,
        $id
      ]);
    } catch (Exception $e) {
      return 500;
    }
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
    $stmt = $this->container->db->prepare(
      'UPDATE instructor_adi_license_verifications
      SET status = 2
      WHERE user_id = ?'
    );

    try {
      $stmt->execute([$instructor_id]);
    } catch (Exception $e) {
      return 500;
    }
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

  
  /**
   * get instructors with adi licence status in review
   */
  public function get_instructors_in_review() {
    $stmt = $this->container->db->prepare(
      "SELECT instructors.id AS id, first_name, surname, adi_license_no, gender,
        email, contact_number, avatar_url, uv.adi_license_src, uv.id AS adi_licence_id
      FROM instructors 
      INNER JOIN instructor_adi_license_verifications AS uv 
        ON uv.user_id = instructors.id
      WHERE uv.status = 2"
    );

    try {
      $stmt->execute();
    } catch (PDOException $e) {
      return false;
    }

    return $stmt->fetchAll();
  }


  /**
   * update instructor adi licence status 
   */
  public function update_adi_licence_status($id, $status, $reject_reason) {
    $stmt = $this->container->db->prepare(
      "UPDATE instructor_adi_license_verifications 
      SET status = ?, reject_reason = ?
      WHERE id = ?"
    );

    try {
      $stmt->execute([$status, $reject_reason, $id]);
    } catch (PDOException $e) {
      return 500;
    }
  }


  /**
   * update instructor verified property
   */
  public function update_verified($id, $verified) {
    $stmt = $this->container->db->prepare(
      "UPDATE instructors SET verified = ? WHERE id = ?"
    );

    try {
      $stmt->execute([$verified, $id]);
    } catch (PDOException $e) {
      return 500;
    }
  }


  /**
   * get instructor id of adi licence verification 
   */
  public function get_instructor_id_of_adi_licence_verification($id) {
    $stmt = $this->container->db->prepare(
      "SELECT instructors.id AS instructor_id
      FROM instructor_adi_license_verifications AS iv
      INNER JOIN instructors
        ON instructors.id = iv.user_id
      WHERE iv.id = ?"
    );

    try {
      $stmt->execute([$id]);
    } catch (PDOException $e) {
      return 500;
    }

    return $stmt->fetch()['instructor_id'];
  }

  /**
   * get all verified instructors 
   */
  public function get_verified_instructors() {
    $stmt = $this->container->db->prepare(
      "SELECT first_name, surname, email, ic.postcode, ic.longitude, ic.latitude
      FROM instructors
      LEFT JOIN instructor_coverage AS ic
        ON ic.user_id = instructors.id
      WHERE instructors.verified = true"
    );

    try {
      $stmt->execute();
    } catch (PDOException $e) {
      return 500;
    }

    return $stmt->fetchAll();
  }
}