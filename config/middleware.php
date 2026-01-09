<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2019 - 2026 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

use Piton\Library\Handlers\ErrorRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

/**
 * Load Middleware
 *
 * Middleware is called by Slim in reverse order (bottom up)
 */

$app->add(new Piton\Middleware\ResponseHeaders($container->get('settings')));
$app->add(new Piton\Middleware\LoadSiteSettings($container->get('settings'), $container->get('dataMapper'), $container->get('csrfGuardHandler'), $container->get('sessionHandler')));
$app->addRoutingMiddleware();

// Keep Error Middleware (below) as last in middleware file, Slim executes this first.
$errorMiddleware = $app->addErrorMiddleware($config['displayErrorDetails'], true, true, $container->get('logger'));
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
