<?php

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

        if ($validate_user_model) {
            return $response->withJson($validate_user_model, 403);
        }

        $stmt = $this->container->db->prepare(
            "INSERT INTO users (name, email, postcode, range, account_type)
            VALUES (?,?,?,?,?)"
        );

        try {
            $stmt->execute([
                $user_details->name, 
                $user_details->email, 
                $user_details->postcode, 
                $user_details->range,
                2
            ]);
        } catch (Exception $e) {
            return $response->withJson($e, 500);
        }

        return $response->withJson('new user added', 200);
    }


    /**
     * abstract user details from parameters in http request and assign to object
     */
    private function get_user_details($request) {
        $user_details = new stdClass;

        $user_details->name = $request->getParam('name');
        $user_details->email = $request->getParam('email');
        $user_details->postcode = $request->getParam('postcode');
        $user_details->range = $request->getParam('range');

        return $user_details;
    }


    /**
     * valdate user input recieved from request
     */
    private function validate_user_model($user_details) {
        if (!$user_details->name) { return 'name is required'; }
        if (!$user_details->email) { return 'email is required'; }
        if (!$user_details->postcode) { return 'postcode is required'; }
        if (!$user_details->range) { return 'range is required'; }

        return false;
    }
}