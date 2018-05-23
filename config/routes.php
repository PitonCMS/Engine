<?php
/**
 * Public Application Routes
 */

// Load dynamic page by /url. Keep as second to last route
$app->get('/{url}', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->showPage($request, $response, $args);
})->setName('showPage');

// Home page '/' is always the last route, the default
$app->get('/', function ($request, $response, $args) {
    $args['url'] = 'home';
    return (new Piton\Controllers\IndexController($this))->showPage($request, $response, $args);
})->setName('home');
