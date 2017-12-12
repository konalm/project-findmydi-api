<?php

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

class UserController 
{
    protected $container;

    public function __construct (Slim\Container $container) {
        $this->container = $container;
    }

    /**
     * create new user model (usually registered driving instructor)
     */
    public function save_user ($request, $response, $args) {
        $user_details = $this->get_user_details($request);
        $validate_user_model = $this->validate_user_model($user_details);
        $user_details = $this->clean_user_details($user_details);

        if ($validate_user_model) {
            return $response->withJson($validate_user_model, 403);
        }

        // $postcode_stats = $this->get_long_and_lat($user_details->postcode); 

        // if (!$postcode_stats) {
        //   return $response->withJson('not a valid postcode', 404);
        // }

        $stmt = $this->container->db->prepare(
            "INSERT INTO users 
              (name, email, password, account_type)
              VALUES (?,?,?,?)"
        );

        try {
            $stmt->execute([
                $user_details->name, 
                $user_details->email, 
                password_hash($user_details->password, PASSWORD_BCRYPT),
                2,
            ]);
        } catch (Exception $e) {
            return $response->withJson($e, 500);
        }

        return $response->withJson('new user added', 200);
    }

    /**
     * send postcode to api.postcode.io to check postcode is valid
     * and return the longitude and latitude
     */
    private function get_long_and_lat($postcode) {
        $client = new \GuzzleHttp\Client();

        try {
            $res = $client->request('GET', "http://api.postcodes.io/postcodes/${postcode}");
        } catch (RequestException $e) {
            return false;
        }

        return json_decode($res->getBody());
    }


    /**
     * abstract user details from parameters in http request and assign to object
     */
    private function get_user_details($request) {
        $user_details = new stdClass;

        $user_details->name = $request->getParam('name');
        $user_details->email = $request->getParam('email');
        $user_details->password = $request->getParam('password');

        // $user_details->postcode = $request->getParam('postcode');
        // $user_details->range = $request->getParam('range');

        return $user_details;
    }


    /**
     * valdate user input recieved from request
     */
    private function validate_user_model($user_details) {
        if (!$user_details->name) { return 'name is required'; }
        if (!$user_details->email) { return 'email is required'; }
        if (!$user_details->password) { return 'password is required'; }

        // if (!$user_details->postcode) { return 'postcode is required'; }
        // if (!$user_details->range) { return 'range is required'; }

        return false;
    }

    /**
     * remove whitespace from beginning and end of user details
     */
    private function clean_user_details($user_details) {
        $user_details->name = trim($user_details->name);
        $user_details->email = trim($user_details->email);
        // $user_details->password = trim($user_details->password);

        // $user_details->postcode = trim($user_details->postcode);
        // $user_details->range = trim($user_details->range);

        return $user_details;
    }
}