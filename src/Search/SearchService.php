<?php 

namespace App\Search;

use Underscore\Types\Arrays;


class SearchService 
{
  /**
   * build url of users position and instructors coverage's to be sent to 
   * google's distance matrix api
   */
  public function build_distance_matrix_request_url($origin_postcode, $instructors) {
    $origin_latitude = round($origin_postcode->latitude, 5);
    $origin_longitude = round($origin_postcode->longitude, 5);

    $matrix_url = 
        "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&"
        . "origins=${origin_latitude},${origin_longitude}&"
        . "destinations=";
    
    foreach ($instructors as $key => $instructor) {
      $matrix_url .= $instructor['latitude'] . 
      "," . 
      $instructor['longitude'];

      if ($key !== count($instructors) - 1) {
          $matrix_url .= '|';
      }
    }

    $matrix_url .= "&key=AIzaSyDmDmEpOyYmT5K7gggljv-lEySLmlYJdvQ";
    $matrix_url = preg_replace('/\s+/', '', $matrix_url);

    return $matrix_url;
  }


  /**
   * return all instructors who's range is within origin 
   */
  public function get_instructors_in_range($instructors, $maps_res) {
    $instructors_in_range = [];

    foreach ($instructors as $key => $instructor) {
        if (
            $instructor['range'] >= 
            ($maps_res[$key]->distance->value / 1609.34)
        ) {
            array_push($instructors_in_range, $instructor);
        }
    }
    
    return $instructors_in_range;
  }


  /**
   * send http request to google distance matrix api to get distance 
   * all destinations are from origin
   */
  public function google_matrix_api_request ($url) {
    $client = new \GuzzleHttp\Client();

    try {
        $res = $client->request('GET', $url);
    } catch (RequestException $e) {
        return false;
    }

    $res = json_decode($res->getBody());
    return $res;
  }

  /**
   * remove properties that are not required for instructors
   */
  public function remove_properties_for_searched_instructors($instructors) {
    return Arrays::invoke($instructors, function($value) {
      unset($value['longitude']);
      unset($value['latitude']);
      unset($value['range']);
      unset($value['postcode']);
      return $value;
    });
  }
}