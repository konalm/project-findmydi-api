<?php 

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

require __DIR__ . '/../../src/services/PostcodeService.php';

class SearchController
{
    protected $container;

    public function __construct (Slim\Container $container) {
        $this->container = $container;
        $this->postcode_service = new PostcodeService();
    }

  
    /**
     * return driving instructors whose range is within origin of postcode
     */
    public function search_instructors ($request, $response, $args) {
        $postcode = $args['postcode'];

        $postcode_service = new PostcodeService();
        $postcode_data = $postcode_service->get_postcode_data($postcode);

        if (!$postcode_data) {
            return $response->withJson('issue using postcode', 500);
        }

        $drivers = $this->get_drivers();

        $distance_matrix_url = 
            $this->buils_distance_matrix_request_url($postcode_data, $drivers);
        
        $maps_res = $this->http_request_to_google_matrix_api($distance_matrix_url);

        if (!$maps_res) {
            return $response->withJson('issue sending request to google matrix api', 500);
        }

        $instructors_in_range = 
            $this->get_instructors_in_range($drivers, $maps_res->rows[0]->elements);

        return $response->withJson($instructors_in_range, 200);
    }


    /**
     * send http request to google distance matrix api to get distance 
     * all destinations are from origin
     */
    private function http_request_to_google_matrix_api ($url) {
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
     * return all instructors who's range is within origin 
     */
    private function get_instructors_in_range($instructors, $maps_res) {
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
     * get all registered driving instructors
     */
    private function get_drivers () {
        $query_res = 
            $this->container->db
                ->query(
                    "SELECT name, email, range, postcode, distance_longitude, distance_latitude
                        FROM public.users 
                        WHERE account_type = 2
                            AND distance_longitude IS NOT NULL
                            AND distance_latitude IS NOT NULL"
                )
                ->fetchAll();
        
        return $query_res;
    }


    /**
     * build the url that will be sent to google maps distance matrix api to determine 
     * the distance in miles every user is from the orgin longitude and latitude
     */
    private function buils_distance_matrix_request_url ($origin_postcode, $drivers) {
        $origin_latitude = round($origin_postcode->latitude, 5);
        $origin_longitude = round($origin_postcode->longitude, 5);

        $matrix_url = 
            "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&"
            . "origins=${origin_latitude},${origin_longitude}&"
            . "destinations=";
        
        foreach ($drivers as $key => $driver) {
            $matrix_url .= $driver['distance_latitude'] . 
            "," . 
            $driver['distance_longitude'];

            if ($key !== count($drivers) - 1) {
                $matrix_url .= '|';
            }
        }

        $matrix_url .= "&key=AIzaSyDmDmEpOyYmT5K7gggljv-lEySLmlYJdvQ";
        $matrix_url = preg_replace('/\s+/', '', $matrix_url);

        return $matrix_url;
    }
}