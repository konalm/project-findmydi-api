<?php 

$app->get('/review-by-token/{token}', 'ReviewController:get_review_by_token');
$app->get('/reviews', 'ReviewController:get_instructor_reviews')->add($inst_auth);
$app->post('/reviews', 'ReviewController:save_review');

$app->post('/email-review-invite', 'ReviewController:send_review_invite')->add($inst_auth);

$app->get('/review-requests', 'ReviewController:get_review_requests')->add($inst_auth);
$app->post('/review-requests', 'ReviewController:save_review_request');

$app->get('/verify-review-token/{token}', 'ReviewController:verify_review_token');

$app->delete('/review-invite-tokens/{invite_id}', 'ReviewController:cancel_review_invite')
  ->add($inst_auth);

$app->get('/resend-review-invite/{invite_id}', 'ReviewController:resend_review_invite')
  ->add($inst_auth);