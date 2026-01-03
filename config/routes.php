<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 - 2026 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

use Piton\Controllers\FrontController;

/**
 * Public Piton Application Routes
 */

// Get Route Collector to test if a named route override has been defined in the project
$router = $app->getRouteCollector();
// var_dump($router);
// die;

// XHR: Submit contact message, do not override
$app->post('/submitmessage', function ($args) {
    return (new FrontController($this))->submitMessage();
})->add('csrfGuardHandler')->setName('submitMessage');

// Load page by /page or /collection/page. Keep as second to last route
if (!$router->hasNamedRoute('showPage')) {
    $app->get('/{slug1}[/{slug2}]', function ($args) {
        return (new FrontController($this))->showPage($args);
    })->setName('showPage');
}

// Home page '/' is always the last route as default, and an alias for the 'home' route
if (!$router->hasNamedRoute('showPage')) {
    $app->get('/', function ($args) {
        $args['slug1'] = 'home';

        return (new FrontController($this))->showPage($args);
    })->setName('home');
}
