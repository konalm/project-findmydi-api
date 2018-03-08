<?php 

$app->get('/reviews', 'ReviewController:get_instructor_reviews')->add($inst_auth);
$app->post('/email-review-invite', 'ReviewController:send_review_invite')->add($inst_auth);
$app->get('/review-requests', 'ReviewController:get_review_requests')->add($inst_auth);

