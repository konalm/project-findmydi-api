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
    if (!$this->token_service->verify_token($request)) {
      return $response->withJson('Not Authorized', 406);
    }

    $instructor_id = $this->token_service->get_decoded_user($request)->id;

    $profile = new \stdClass();
    $profile->hourly_rate = $request->getParam('hourlyRate');
    $profile->contact_number = $request->getParam('contactNumber');
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


   /**
     * get profile pic out of request, if no reqest then return error
     * assign name to profile pic for user id and move to uploads directory
     */
    public function update_avatar ($request, $response, $args) {
      if (!$this->token_service->verify_token($request)) {
        return $response->withJson('Not Authenticated', 401);
      }

      $uploaded_files = $request->getUploadedFiles();
      $avatar = $uploaded_files['file'];

      if (!$avatar) {
        return $response->withJson('no avatar found', 403);
      }

      $user_id = $this->token_service->get_decoded_user($request)->id;
      $avatar_name = $user_id . '.jpg';

      $move_to_dir = $this->container->getUploadDir .
        'instructorAvatar/' .
        $avatar_name;

      $avatar->moveTo($move_to_dir);
      $this->repo->update_avatar($user_id, "uploads/instructorAvatar/{$avatar_name}");

      return $response->withJson('saved image', 200);
    }

    /**
     * upload adi license for review  
     * if adi license upload already exists -> update status to review
     * if doesn't already exist -> create adi license model 
     * save adi license image in dir 
     */
    public function upload_adi_licence_for_review ($request, $response, $args) {
      if (!$this->token_service->verify_token($request)) {
        return $response->withJson('Not Authorized', 406);
      }

      $user_id = $this->token_service->get_decoded_user($request)->id;
      $uploaded_files = $request->getUploadedFiles();
      $adi_license_photo = $uploaded_files['file'];

      if (!$adi_license_photo) {
        return $response->withJson('no adi license photo found', 403);
      }

      $move_to_dir = $this->container->getUploadDir . 
        'adiLicenceVerification/' .
        $user_id . '.jpg';
      
      $adi_license_photo->moveTo($move_to_dir);
 
      
      if ($this->repo->get_adi_licence($user_id)) {
        $this->repo->update_adi_licence($user_id);
        return $response->withJson('resubmitted adi licence for review');
      } 

      $this->repo->create_adi_licence($user_id, "uploads/adiLicenceVerification/{$user_id}.jpg");

      return $response->withJson('submitted adi license for review');
    }

    /**
     * get instructors with adi photo licence in review 
     */
    public function get_instructors_in_review ($request, $response, $args) {
      if (!$this->token_service->verify_super_admin_token($request)) {
        return $response->withJson('Not Authorized', 406);
      }

      $instructors_in_review = $this->repo->get_instructors_in_review();

      return $response->withJson($instructors_in_review, 200);
    }

    /**
     * verify user is super admin
     * update status of instructors adi licence
     */
    public function update_adi_licence_status($request, $response, $args) {
      if (!$this->token_service->verify_super_admin_token($request)) {
        return $response->withJson('Not Authorized', 406); 
      }

      if ($validation = $this->service->validate_adi_licence_status_update($request)) {
        return $response->withJson($validation, 403);
      }

      $id = $args['id'];
      $status = $request->getParam('status');

      $reject_reason = $request->getParam('status') == 0 ?
        $request->getParam('rejectReason') : null; 

      if ($this->repo->update_adi_licence_status($id, $status, $reject_reason) === 500) {
          return $response->withJson('error updating adi licence status', 500);
      }

      return $response->withJson('instructor adi licence status updated', 200);
    }
} 