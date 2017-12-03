<?php

class HomeController
{
    protected $container;
    // protected $app;

    public function __construct(Slim\Container $container) {
        $this->container = $container;
        // $this->app = $app;
    }

    /**
     * test response from controller
     */
    public function home ($request, $response, $args) {
        $query_res = 
            $this->container->db
                ->query("SELECT * FROM public.test")
                ->fetchAll();
        
        return $response->withJson($query_res, 200);
    }

    /**
     * 
     */
    public function play ($request, $response, $args) {
        $user_details = new stdClass();
        $name = $request->getParam('name');
        $email = $request->getParam('email');
        $account_type = $request->getParam('accountType');
        $postcode = $request->getParam('postcode');

        /* further protection again sql injection ?? */ 

        $stmt = $this->container->db->prepare(
            "INSERT INTO users (name, email, postcode, account_type) 
            VALUES (?, ?, ?, ?)"
        );

        try {
            $stmt->execute([$name, $email, $postcode, $account_type]);
        } catch (Exception $e) {
            return $response->withJson($e, 500);
        }

        return $response->withJson('new user added', 200);
    }
}