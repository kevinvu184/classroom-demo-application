<?php

$app->get('/', function (Request $request, Response $response) {
    return $response->withRedirect('/books');
})->setName('home');