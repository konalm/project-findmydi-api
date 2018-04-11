<?php 

$app->post('/stats', 'StatsController:create_stat');
$app->get('/stats', 'StatsController:get_user_stats');
