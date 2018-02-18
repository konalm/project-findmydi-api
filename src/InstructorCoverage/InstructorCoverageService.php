<?php 

namespace App\InstructorCoverage;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use App\Services\TokenService;

class InstructorCoverageService 
{


  /**
   * valdate instructor input recieved from request
   */
  public function validate_instructor_values($instructor_coverage) {
    if (!$instructor_coverage->postcode) { return 'postcode is required'; }
    if (!$instructor_coverage->range) { return 'range is required'; }

    return false;
  }

  /**
   * validate instructor coverage of region type
   */
  public function validate_region_coverage($coverage) {
    if (!$coverage->region) {
      return 'region is required';
    }

    if (!$coverage->long) {
      return 'longitude is required';
    }

    if (!$coverage->lat) {
      return 'latitude is required';
    }

    if (!$coverage->range) {
      return 'range is required';
    }
  }


  /**
   * send postcode to api.postcode.io to check postcode is valid
   * and return the longitude and latitude
   */
  public function get_long_and_lat($postcode) {
    $client = new \GuzzleHttp\Client();

    try {
      $response= $client->request(
        'GET', 
        "http://api.postcodes.io/postcodes/${postcode}"
      );
    } catch (RequestException $e) {
      return false;
    }

    $result = json_decode($response->getBody());
    
    return $result->result;
  }
}