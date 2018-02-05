<?php 

namespace App\Services;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class PostcodeService 
{
    /**
     * 
     */
    public function get_postcode_data($postcode) {
        $client = new \GuzzleHttp\Client();

        try {
            $res = $client->request('GET', "http://api.postcodes.io/postcodes/${postcode}");
        } catch (RequestException $e) {
            return false;
        }

        $res = json_decode($res->getBody());
        
        return $res->result;
    }
}