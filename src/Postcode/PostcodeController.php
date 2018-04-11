<?php 

namespace App\Postcode;

use \Interop\Container\ContainerInterface as ContainerInterface;
use App\Postcode\PostcodeService; 
use App\Services\TokenService;


class PostcodeController 
{
  protected $container; 

  public function __construct(ContainerInterface $container) {
    $this->container = $container; 
    $this->service = new PostcodeService();
    $this->token_service = new TokenService();    
  }

  /** 
   * verify postcode is legit and get long & lat 
   */
  public function get_postcode_lnglat($request, $response, $args) {
    if (!$this->token_service->verify_token($request)) {
      return $response->withJson('Not Authorized', 406);
    }

    $postcode = $args['postcode'];

    if (!$postcode) {
      return $response->withJson('no postcode', 422);
    }

    if (!$postcode_lnglat = $this->service->get_postcode_data($postcode)) {
      return $response->withJson('invalid postcode', 422);
    }

    return $response->withJson([
      'long' => $postcode_lnglat->longitude,
      'lat' => $postcode_lnglat->latitude
    ], 200);
  }
}