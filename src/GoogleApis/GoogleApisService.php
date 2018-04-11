<?php 

namespace App\GoogleApis;

use GuzzleHttp\Client; 


class GoogleApisService 
{
  /**
   * 
   */
  public function googleapis_autocomplete_httprequest($search_term) {
    $client = new Client(); 

    $url = "https://maps.googleapis.com/maps/api/place/autocomplete/json" .
      "?input={$search_term}" .
      "&types=(regions)" . 
      "&key=" . getenv('GOOGLE_API_KEY');

    try {
      $res = $client->request('GET', $url);
    } catch (RequestException $e) {
      return false;
    }

    return json_decode($res->getBody());
  }

  
  /** 
   * 
   */
  public function googleapis_geocode_httprequest($address) {
    $client = new Client();
    
    $url = "https://maps.googleapis.com/maps/api/geocode/json" .
      "?address={$address}" .
      "&key=AIzaSyDmDmEpOyYmT5K7gggljv-lEySLmlYJdvQ";

    try {
      $res = $client->request('GET', $url);
    } catch (RequestException $e) {
      return false;
    }

    return json_decode($res->getBody());
  }
}