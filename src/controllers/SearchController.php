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
     * 
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
     * search for driving instructors within range of origin
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

        $this->get_instructors_in_range($drivers, $maps_res->rows[0]->elements);

        return $response->withJson($maps_res, 200);
    }

    /**
     * 
     */
    private function get_instructors_in_range($instructors, $maps_res) {
        error_log('get instructors in range');
        error_log(gettype($maps_res));

        error_log('maps res -->');
        error_log(json_encode($maps_res));

        foreach ($instructors as $key => $inst) {
            error_log('looking at instructor');
            error_log($inst['range']);
            error_log('<--------------');

            /* look at the range from origin */ 
            $maps_data = $maps_res[$key];
            error_log( json_encode($maps_data) );
            error_log('------------------>');
            error_log('<------------------');
        }

    }

    /**
     * 
     */
    private function get_drivers () {
        $query_res = 
            $this->container->db
                ->query(
                    "SELECT range, distance_longitude, distance_latitude
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