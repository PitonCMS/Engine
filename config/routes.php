<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

use Piton\Controllers\FrontController;

/**
 * Public Piton Application Routes
 */

// phpcs:disable PHPCompatibility.FunctionDeclarations.NewClosure.ThisFoundOutsideClass

// XHR: Submit contact message, do not override
$app->post('/submitmessage', function ($request, $response, $args) {
    return (new FrontController($request, $response, $this))->submitMessage();
})->add('csrfGuardHandler')->setName('submitMessage');

// TODO: Add test if named route exists to allow overrides in project routes

// Load page by /page or /collection/page. Keep as second to last route
$app->get('/{slug1}[/{slug2}]', function ($request, $response, $args) {
    return (new FrontController($request, $response, $this))->showPage($args);
})->setName('showPage');


// Home page '/' is always the last route as default, and an alias for the 'home' route
$app->get('/', function ($request, $response, $args) {
    $args['slug1'] = 'home';

    return (new FrontController($request, $response, $this))->showPage($args);
})->setName('home');
