<?php 

namespace App\Search;

use \Interop\Container\ContainerInterface as ContainerInterface;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use App\Search\SearchService;
use App\Instructor\InstructorRepo;
use App\InstructorCoverage\InstructorCoverageRepo;
use App\Services\PostcodeService;


class SearchController
{
    protected $container;

    public function __construct (ContainerInterface $container) {
      $this->container = $container;
      $this->service = new SearchService();
      $this->postcode_service = new PostcodeService();
      $this->instructor_repo = new InstructorRepo($container);
      $this->instructor_coverage_repo = new InstructorCoverageRepo($container);
    }

  
    /**
     * get verified driving instructors whose range is within origin 
     * of user's postcode
     */
    public function search_instructors ($request, $response, $args) {
      $postcode_data = $this->postcode_service->get_postcode_data($args['postcode']);

      if (!$postcode_data) {
        return $response->withJson('issue using postcode', 500);
      }

      $instructors =  
        $this->instructor_coverage_repo->get_coverages_for_verified_instructors();

      $distance_matrix_url = 
        $this->service-> 
        build_distance_matrix_request_url($postcode_data, $instructors);

      if (!$maps_res = $this->service->google_matrix_api_request($distance_matrix_url)) {
        return $response->withJson('issue sending request to google matrix api', 500);
      }

      $instructors_in_range = 
        $this->service->get_instructors_in_range($instructors, $maps_res->rows[0]->elements);

      /* remove no longer required properties */ 
      $instructors_in_range = 
        $this->service->
        remove_properties_for_searched_instructors($instructors_in_range);

      /* avoid returning same instructor more that once */ 
      $instructors_in_range = array_unique($instructors_in_range, SORT_REGULAR);

      return $response->withJson($instructors_in_range, 200);
    }
}