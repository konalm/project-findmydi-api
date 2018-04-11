<?php 

namespace App\Stats;

use App\Stats\StatsService; 
use App\Stats\StatsRepo;
use App\Services\TokenService;


class StatsController 
{
  public function __construct (\Slim\Container $container) {
    $this->container = $container;   
    $this->service = new StatsService();
    $this->repo = new StatsRepo($container);
    $this->token_service = new TokenService();
  }


  /**
   * create stat
   */
  public function create_stat ($request, $response, $args) {
    $stat = new \stdClass();
    $stat->event = $request->getParam('event');
    $stat->instructor_id = $request->getParam('instructorId');

    if ($val = $this->service->validate_new_stat($stat)) {
      return $response->withJson($val, 422);
    }

    $saved_stat = $this->repo->save_stat($stat);

    return $response->withJson([
        message => 'stat created successfully',
        data => $saved_stat 
    ]);
  }


  /**
   * get users statistics
   */
  public function get_user_stats ($request, $response, $args) {
    $user_id = $this->token_service->get_decoded_user($request)->id;
    $user_stats = $this->repo->get_stats($user_id);

    return $response->withJson($user_stats);
  }
}