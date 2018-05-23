<?php
/**
 * Public Application Routes
 */

// Home page '/home' in case someone tries to load the home tempate (keyword)
$app->get('/home', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->homePage($request, $response, $args);
});

// Load dynamic page by /url. Keep as second to last route
$app->get('/{url}', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->showPage($request, $response, $args);
})->setName('showPage');

// Home page '/' is always the last route, the default
$app->get('/', function ($request, $response, $args) {
    return (new Piton\Controllers\IndexController($this))->homePage($request, $response, $args);
})->setName('home');
