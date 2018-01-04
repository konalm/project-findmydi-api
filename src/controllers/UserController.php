<?php

namespace App\Controllers;

use Slim\Http\UploadedFile;
use \Interop\Container\ContainerInterface as ContainerInterface;

use App\Services\TokenService;
use App\Services\UserService;
use App\Repos\UserRepo;


class UserController 
{
    
  protected $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
    $this->tokenService = new TokenService();
    $this->token_service = new TokenService();

    $this->user_service = new UserService($container);
    $this->user_repo = new UserRepo($container);
  }

  /**
   * get user data encoded in jwt token 
   */
  public function get_user($request, $response, $args) {
    $token_service = new TokenService();

    if (!$token_service->verify_token($request)) {
    return $response->withJson('Not Authorized', 406);
    }

    $user = $token_service->get_decoded_user($request);

    return $response->withJson($user, 200);
  }

  /**
   * get id for user in token and then use it to the the user from the DB
   * (we use this incase the users info has been updated since last token created)
   */
  public function get_user_from_db($request, $response, $args) {
    if (!$this->token_service->verify_token($request)) {
    return $response->withJson('Not Authorized', 406);
    }

    $user_from_token = $this->token_service->get_decoded_user($request);
    $user_from_db = $this->user_repo->get_user($user_from_token->id);

    return $response->withJson($user_from_db);
  }

    /**
     * create new user model (usually registered driving instructor)
     */
    public function save_user ($request, $response, $args) {
      $user_details = $this->get_user_details($request);
      $validate_user_model = $this->validate_user_model($user_details);
      $user_details = $this->clean_user_details($user_details);

      if ($validate_user_model) {
        return $response->withJson($validate_user_model, 403);
      }

      $stmt = $this->container->db->prepare(
        "INSERT INTO users 
          (name, email, password, account_type)
          VALUES (?,?,?,?)"
      );

      try {
        $stmt->execute([
          $user_details->name, 
          $user_details->email, 
          password_hash($user_details->password, PASSWORD_BCRYPT),
          2,
        ]);
      } catch (Exception $e) {
        return $response->withJson($e, 500);
      }

      return $response->withJson('new user added', 200);
    }


    /**
     * save verification details of instructor and store the uploaded photo 
     * of instructors api license
     * (will be used for review by super admin)
     */
    public function save_verification_details($request, $response, $args) {
      if (!$this->token_service->verify_token($request)) {
        return $response->withJson('Not Authorized', 406);
      }

      $credentials = new \stdClass();
      $credentials->user_id = $this->token_service->get_decoded_user($request)->id;
      $credentials->adi_no = $request->getParam('adiNo');
      $credentials->license_since_month = $request->getParam('licenseSinceMonth');
      $credentials->license_since_year = $request->getParam('licenseSinceYear');
    
      $this->user_service->upload_adi_license_photo($request);

      $save_verification_credentials = 
        $this->user_repo->save_verification_credentials($credentials);
    
      $this->user_repo->update_instructor_verification($credentials->user_id, 2);
    
      if (!$save_verification_credentials['success']) {
        return $response->withJson($save_verification_credentials['message'], 500);
      }

      return $response->withJson($save_verification_credentials['message'], 200);
    }


    /**
     * get profile pic out of request, if no reqest then return error
     * assign name to profile pic for user id and move to uploads directory
     */
    public function upload_avatar ($request, $response, $args) {
      if (!$this->token_service->verify_token($request)) {
        return $response->withJson('Not Authenticated', 401);
      }

      $uploaded_files = $request->getUploadedFiles();
      $avatar = $uploaded_files['file'];

      if (!$avatar) {
        return $response->withJson('no profile pic found', 403);
      }

      $user = $this->token_service->get_decoded_user($request);
      $extension = pathinfo($avatar->getClientFilename(), PATHINFO_EXTENSION);
      $avatar_name = $user->id . '.jpg';

      $move_to_dir = $this->container->getUploadDir .
        'instructorAvatar/' .
        $avatar_name;

      $avatar->moveTo($move_to_dir);
      $this->user_repo->update_instructor_has_avatar($user->id, 1);

      return $response->withJson('saved image', 200);
    }


    /**
     * get user and verify is a super admin
     */
    public function get_super_admin ($request, $response, $args) {
      if (!$this->token_service->verify_token($request)) {
        return $response->withJson('Not Authenticated', 401);
      }

      $super_admin = $this->token_service->get_decoded_user($request);

      if ($super_admin->role !== 222) {
        return $response->withJson('Not Authorized', 406);
      }

      return $response->withJson($super_admin, 200);
    }


    /**
     * 
     */
    public function get_users_verification_credentials($request, $response, $args) {
      if (!$this->token_service->verify_super_admin_token($request)) {
        return $response->withJson('Not Authorized', 406);
      }

      $users_verification_credentials = 
        $this->user_repo->get_users_verification_credentials();
    
      if ($users_verification_credentials === 500) {
          return $response->withJson('issue getting instructors', 500);
      }

      return $response->withJson($users_verification_credentials, 200);
    }

    /**
     * 
     */
    public function update_instructor_verification($request, $response, $args) {
      if (!$this->token_service->verify_super_admin_token($request)) {
        return $response->withJson('Not Authorized', 406);
      }

      $id = $args['id'];
      $new_status = $request->getParam('status');

      $update_instructor_verification = 
        $this->user_repo->update_instructor_verification($id, $new_status); 
    
      if (!$update_instructor_verification) {
        return $response->withJson('issue updating verification', 500);
      }

      return $response->withJson(
        'instructor ' . $id . ' verification updated to ' . $new_status
      );
    }

    /**
     * 
     */
    public function update_instructor_coverage($request, $response, $args) {
      if (!$this->token_service->verify_token($request)) {
        return $response->withJson('Not Authorized', 406);
      }

      $new_coverage = new \stdClass();
      $new_coverage->postcode = $request->getParam('postcode');
      $new_coverage->radius = $request->getParam('radius');

      $user = $this->token_service->get_decoded_user($request);

      $postcode_stats = 
        $this->user_service->get_long_and_lat($new_coverage->postcode)->result;

      $new_coverage->longitude = $postcode_stats->longitude;
      $new_coverage->latitude = $postcode_stats->latitude;

      $update_instructor_coverage = 
        $this->user_repo->update_instructor_coverage($user->id, $new_coverage);

      return $response->withJson('updated instructor coverage', 200); 
    }


    /**
     * abstract user details from parameters in http request and assign to object
     */
    private function get_user_details($request) {
      $user_details = new \stdClass;

      $user_details->name = $request->getParam('name');
      $user_details->email = $request->getParam('email');
      $user_details->password = $request->getParam('password');

      return $user_details;
    }


    /**
     * valdate user input recieved from request
     */
    private function validate_user_model($user_details) {
      if (!$user_details->name) { return 'name is required'; }
      if (!$user_details->email) { return 'email is required'; }
      if (!$user_details->password) { return 'password is required'; }

      return false;
    }

    /**
     * remove whitespace from beginning and end of user details
     */
    private function clean_user_details($user_details) {
      $user_details->name = trim($user_details->name);
      $user_details->email = trim($user_details->email);

      return $user_details;
    }
}