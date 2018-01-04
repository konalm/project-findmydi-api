<?php 

namespace App\Instructor;

class InstructorRepo 
{
  public function __construct (\Slim\Container $container) {
    $this->container = $container; 
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
}