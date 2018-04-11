<?php

namespace App\GoogleApis;

use \Interop\Container\ContainerInterface as ContainerInterface;
use GuzzleHttp\Client; 
use App\GoogleApis\GoogleApisService; 
use App\Services\TokenService;


class GoogleApisController
{
  protected $container;

  public function __construct (ContainerInterface $container) {
    $this->container = $container; 
    $this->service = new GoogleApisService();
    $this->token_service = new TokenService();    
  }


  /**
   *  get predictions from googleapis api of places 
   * loop through results and only return regions in the UK
   */
  public function get_googleapis_autocomplete_regions($request, $response, $args) {
    $search_term = $args['search_term'];

    $request_res = 
      $this->service->googleapis_autocomplete_httprequest($search_term);
 
    $region_predictions = []; 

    /* obtain results that contain three terms, the first term will be country, 
      second will be city and 3rd region, meaning results with three terms
      will be a region */
    foreach($request_res->predictions as $prediction) {
      if (
        sizeof($prediction->terms) > 2 && 
        $prediction->terms[2]->value === 'UK'
      ) {
        array_push($region_predictions, $prediction);
      }
    }
    
    return $response->withJson($region_predictions, 200);
  }


  /**
   * send request to google geocode api to geocode data of address
   * only return result if type is region
   */
  public function get_googleapis_geocode($request, $response, $args) {
    if (!$this->token_service->verify_token($request)) {
      return $response->withJson('Not Authorized', 406);
    }

    $address = $args['address'];

    if (!$geocode_data = $this->service->googleapis_geocode_httprequest($address)) {
      return $response->withJson('internal server error', 500);
    }

    if (sizeof($geocode_data->results) === 0) {
      return $response->withJson('address not found', 422);
    }

    $geocode = $geocode_data->results[0];
    $address = explode(',', $geocode->formatted_address);

    /* geocode formatted address pattern is Country, City then Region so if 
      address contains three parts of data then it is a region */     
    if (sizeof($address) < 3) {
      return $response->withJson(
        'no region found, it appears you may have entered a country or city', 
        422
      );
    }

    return $response->withJson($geocode, 200);
  }
}