<?php 

namespace App\InstructorCoverage;

use \Interop\Container\ContainerInterface as ContainerInterface;

use App\InstructorCoverage\InstructorCoverageService;
use App\InstructorCoverage\InstructorCoverageRepo;

use App\Services\TokenService;


class InstructorCoverageController 
{
  protected $container;

  public function __construct(ContainerInterface $container) {
    $this->container = $container;
    $this->service = new InstructorCoverageService();
    $this->repo = new InstructorCoverageRepo($container);
    $this->token_service = new TokenService();
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

    $user = $this->token_service->get_decoded_user($request);

    $instructor_coverage = new \stdClass();
    $instructor_coverage->user_id = $user->id;
    $instructor_coverage->postcode = $request->getParam('postcode');
    $instructor_coverage->range = $request->getParam('range');

    $instructor_coverage_validation = 
      $this->service->validate_new_instructor_coverage($instructor_coverage);
    
    $postcode_stats = 
      $this->service->get_long_and_lat($instructor_coverage->postcode)->result;
      
    $instructor_coverage->longitude = $postcode_stats->longitude;
    $instructor_coverage->latitude = $postcode_stats->latitude;
    
    if ($instructor_coverage_validation) {
      return $response->withJson($instructor_coverage_validation, 422);
    }

    $this->repo->save($instructor_coverage);

    return $response->withJson('instructor coverage saved', 200);
  }

  
}