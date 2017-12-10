<?php

$router->get('/', function ($request, $response) {
	$response->send('hello, world');
});

$router->get('/users', function ($request, $response) {
	$response->json(App\Models\User::first())->send();
});

$router->post('/users', function ($request, $response) {
	$response->json(['msg' => 'post /users'])->send();
});

$router->put('/users/{user_id}/articles/{article_id}', function ($request, $response, $user_id, $article_id) {
	$response->json(['msg' => 'put /users/'.$user_id.'/articles/'.$article_id])->send();
});