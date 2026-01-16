<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2019 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

use Piton\Library\Handlers\ErrorRenderer;
use Piton\Middleware\LoadSiteSettings;
use Piton\Middleware\ResponseHeaders;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

/**
 * Load Middleware
 *
 * Middleware is called by Slim in reverse order (bottom up)
 */

$app->add(new ResponseHeaders($container->get('settings'), $container->get('logger')));
$app->add(new LoadSiteSettings($container->get('settings'), $container->get('dataMapper'), $container->get('csrfGuardHandler'), $container->get('sessionHandler'), $container->get('logger')));
$app->addRoutingMiddleware();

// Keep Error Middleware (below) as last in middleware file, Slim executes this first.
$errorMiddleware = $app->addErrorMiddleware($config['displayErrorDetails'], $config['displayErrorDetails'], $config['displayErrorDetails'], $container->get('logger'));
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('text/html', ErrorRenderer::class);

// Set custom 404 handler
$errorMiddleware->setErrorHandler(
    HttpNotFoundException::class,
    function (Request $request, Throwable $exception) use ($container) {
        $handler = $container->get('notFoundHandler');

        return $handler($request, $exception, false, false, false);
    }
);
