<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2019 - 2026 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

/**
 * Load Middleware
 *
 * Middleware is called by Slim in reverse order (bottom up)
 */

$app->add(new Piton\Middleware\ResponseHeaders($container->get('settings')));
$app->add(new Piton\Middleware\LoadSiteSettings($container->get('settings'), $container->get('dataMapper'), $container->get('csrfGuardHandler'), $container->get('sessionHandler')));
$app->addRoutingMiddleware();

// Keep as last loaded middleware
$app->addErrorMiddleware($config['displayErrorDetails'], true, true);
