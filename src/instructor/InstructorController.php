<?php

namespace App\Instructor;

use \Interop\Container\ContainerInterface as ContainerInterface;
use App\Instructor\InstructorService;
use App\Instructor\InstructorRepo;

use App\Services\TokenService;


class InstructorController 
{
  protected $container; 
  
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
    $this->service = new InstructorService();
    $this->repo = new InstructorRepo($container);

    $this->token_service = new TokenService();
  }

  
  /**
   * get instructor 
   */
  public function get_instructor($request, $response, $args) {
    if (!$this->token_service->verify_token($request)) {
      return $response->withJson('Not Authorized', 406);
    }

    $instructor_id = $this->token_service->get_decoded_user($request)->id;
    $instructor = $this->repo->get($instructor_id);

    return $response->withJson($instructor, 200);
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


  /**
   * get instructor profile params from request
   * validate params from request
   * update instructor profile
   */
  public function update_profile($request, $response, $args) {
    error_log('update profile'); 

    if (!$this->token_service->verify_token($request)) {
      return $response->withJson('Not Authorized', 406);
    }

    $instructor_id = $this->token_service->get_decoded_user($request)->id;

    $profile = new \stdClass();
    $profile->hourly_rate = $request->getParam('hourlyRate');
    $profile->offer = $request->getParam('offer');

    $validation = $this->service->validate_instructor_profile($profile);

    if ($validation) {
      return $response->withJson($validation, 422);
    }

    $update_profle = $this->repo->update_profile($instructor_id, $profile);

    if ($update_profle === 500) {
      return $response->withJson('internal issue updating profile', 200);
    }

    return $response->withJson('instructor profile updated');
  }
}