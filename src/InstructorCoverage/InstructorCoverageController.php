<?php 

namespace App\InstructorCoverage;

use \Interop\Container\ContainerInterface as ContainerInterface;

use App\InstructorCoverage\InstructorCoverageService;
use App\InstructorCoverage\InstructorCoverageRepo;

use App\Services\TokenService;
use App\Instructor\InstructorService;


class InstructorCoverageController 
{
  protected $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
    $this->service = new InstructorCoverageService();
    $this->repo = new InstructorCoverageRepo($container);
    $this->token_service = new TokenService();
    $this->instructor_service = new InstructorService($container);
  }

  
  /**
   * validate inputs from client
   * get longitude and latitude from postcode
   * save instructor coverage
   */
  public function save($request, $response, $args) {
    if (!$this->token_service->verify_token($request)) {
      return $response->withJson('Not Authenticated', 401);
    }

    $token_instructor = $this->token_service->get_decoded_user($request);

    $instructor_coverage = new \stdClass();
    $instructor_coverage->user_id = $token_instructor->id;
    $instructor_coverage->postcode = $request->getParam('postcode');
    $instructor_coverage->range = $request->getParam('range');

    $instructor_coverage_validation = 
      $this->service->validate_instructor_values($instructor_coverage);
    
    if ($instructor_coverage_validation) {
      return $response->withJson($instructor_coverage_validation, 422);
    }
    
    $postcode_stats = 
      $this->service->get_long_and_lat($instructor_coverage->postcode);
    
    if (!$postcode_stats) {
      return $response->withJson('invalid postcode', 422);
    }

    $instructor_coverage->longitude = $postcode_stats->longitude;
    $instructor_coverage->latitude = $postcode_stats->latitude;
  
    $this->repo->save_postcode($instructor_coverage);
    $this->instructor_service->check_verified($token_instructor);

    return $response->withJson('instructor coverage saved', 200);
  }


  /**
   * 
   */
  public function save_region($request, $response, $args) {
    error_log('save region');

    if (!$this->token_service->verify_token($request)) {
      return $response->withJson('Not Authenticated', 401);
    }

    $token_instructor = $this->token_service->get_decoded_user($request);

    $coverage = new \stdClass();
    $coverage->user_id = $token_instructor->id;
    $coverage->region = $request->getParam('region');
    $coverage->long = $request->getParam('long');
    $coverage->lat = $request->getParam('lat');
    $coverage->range = $request->getParam('range');

    if ($validation = $this->service->validate_region_coverage($coverage)) {
      return $response->withJson($validation, 422);
    }

    $this->repo->save_region($coverage);

    return $response->withJson('instructor region coverage saved', 200);
  }


  /**
   * validate params sent from client 
   * check instructor is authorized to update instructor coverage model
   * get longitude and latitude from postcode 
   * update instructor coverage model
   */
  public function update($request, $response, $args) {
    if (!$this->token_service->verify_token($request)) {
      return $response->withJson('Not Authenticated', 401);
    }

    $user_id = $this->token_service->get_decoded_user($request)->id;    
    $instructor_coverage = new \stdClass();
    $instructor_coverage->id = $args['id'];
    $instructor_coverage->postcode = $request->getParam('postcode');
    $instructor_coverage->range = $request->getParam('range');

    $validation = $this->service->validate_instructor_values($instructor_coverage);

    if ($validation) {
      return $response->withJson($validation, 422);
    }

    if (!$this->repo->check_instructor_authorized($user_id, $instructor_coverage->id)) {
      return $response
        ->withJson('Not authorized for this instructor coverage model', 406);
    }

    $postcode_long_lat = 
      $this->service->get_long_and_lat($instructor_coverage->postcode);

    if (!$postcode_long_lat) {
      return $response->withJson('invalid postcode', 422);
    }

    $instructor_coverage->longitude = $postcode_long_lat->longitude;
    $instructor_coverage->latitude = $postcode_long_lat->latitude;
    
    $this->repo->update($instructor_coverage);

    return $response->withJson('instructor coverage model updated', 200);
  }

  
  /**
   * check instructor is authorized to delete instructor coverage
   * delete instructor coverage
   */
  public function delete($request, $response, $args) {
    if (!$this->token_service->verify_token($request)) {
      return $response->withJson('Not Authenticated', 401);
    }

    $user_id = $this->token_service->get_decoded_user($request)->id;
    $instructor_coverage_id = $args['id'];
    
    if (!$this->repo->check_instructor_authorized(
      $user_id, $instructor_coverage_id
    )) {
      return $response
        ->withJson('Not authorized for this instructor coverage model', 406);
    }

    $this->repo->delete($instructor_coverage_id);

    return $response->withJson(
        "instructor coverage model deleted {$instructor_coverage_id}", 
        200
    );
  }
}