<?php
/**
 * Public Application Routes
 */
use Piton\Controllers\FrontController;

// Load dynamic page by /url. Keep as second to last route
$app->get('/{url}', function ($args) {
    return (new FrontController($this))->showPage($args);
})->setName('showPage');

// Home page '/' is always the last route, the default, and an alias for the 'home' route
$app->get('/', function ($args) {
    $args['url'] = 'home';
    return (new FrontController($this))->showPage($args);
})->setName('home');
