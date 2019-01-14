<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
use Piton\Controllers\FrontController;

/**
 * Public Piton Application Routes
 */

// Load page by /page or collection/page. Keep as second to last route
$app->get('/{slug1}[/{slug2}]', function ($args) {
    return (new FrontController($this))->showPage($args);
})->setName('showPage');

// Home page '/' is always the last route as default, and an alias for the 'home' route
$app->get('/', function ($args) {
    $args['slug1'] = 'home';
    return (new FrontController($this))->showPage($args);
})->setName('home');
