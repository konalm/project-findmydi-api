<?php

namespace App\Instructor;

use \Interop\Container\ContainerInterface as ContainerInterface;
use App\Instructor\InstructorService;
use App\Instructor\InstructorRepo;

class InstructorController 
{
  protected $container; 
  
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
    $this->service = new InstructorService();
    $this->repo = new InstructorRepo($container);
  }

  /**
   * validate instructor model
   * check if a user already exists with that email
   * save new instructor model
   * (usually happens when new instructor registers)
   */
  public function save($request, $response, $args) {
    $instructor = new \stdClass();
    $instructor->first_name = $request->getParam('firstName');
    $instructor->surname = $request->getParam('surname');
    $instructor->email = $request->getParam('email');
    $instructor->adi_license_no = $request->getParam('adiLicenseNo');
    $instructor->gender = $request->getParam('gender');
    $instructor->password = $request->getParam('password');

    $instructor_validation = 
      $this->service->validate_instructor_details($instructor);

    if ($instructor_validation) {
      return $response->withJson($instructor_validation, 422);
    }

    if ($this->repo->check_email_exists($instructor->email)) {
      return $response->withJson('A user with that email already exists', 403);
    }

    $save_instructor = $this->repo->save($instructor);

    if ($save_instructor === 500) {
      return $response->withJson('issue saving instructor', 500);
    }

    return $response->withJson('new instructor saved', 200);
  }

}