<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

/**
 * Load Base Files and Return Application
 *
 * Set:
 * - Constants
 * - Composer autoloader
 * - Configuration
 * - Load App
 * - Dependencies
 * - Middleware
 * - Routes
 */

use DI\Container;
use Piton\Library\Utilities\Installer;
use Slim\Factory\AppFactory;

// Wrap bootstraping code in an anonymous function to avoid unnecessary globals
return call_user_func(
    function () {
        // Load default configuration settings
        require ROOT_DIR . 'vendor/pitoncms/engine/config/config.default.php';

        // Load local configuration settings as override
        if (file_exists(ROOT_DIR . 'config/config.local.php')) {
            require ROOT_DIR . 'config/config.local.php';
        } else {
            throw new Exception("PitonCMS: No local configuration file found");
        }

        // Set error reporting level based on environment
        if ($config['environment']['production'] === true) {
            // Production
            ini_set('display_errors', 'Off');
            error_reporting(0);
            $config['displayErrorDetails'] = false;
        } else {
            // Development
            error_reporting(E_ALL);
            $config['displayErrorDetails'] = true;
        }

        // Create the PHP-DI container and inject into AppFactory
        // Note, the $container variable is referenced and scoped into in the dependencies.php file that is loaded below
        $container = new Container();
        AppFactory::setContainer($container);

        // Create the Slim application and make the ontainer available to dependencies
        $app = AppFactory::create();

        // Load dependencies into container
        require ROOT_DIR . 'vendor/pitoncms/engine/config/dependencies.php';

        // Load overriding dependencies
        if (file_exists(ROOT_DIR . 'config/dependencies.php')) {
            require ROOT_DIR . 'config/dependencies.php';
        }

        // Check if database tables have been installed
        Installer::run($container->get('database'));

        // Load middleware
        require ROOT_DIR . 'vendor/pitoncms/engine/config/middleware.php';

        // Load admin routes
        require ROOT_DIR . 'vendor/pitoncms/engine/config/routesAdmin.php';

        // Load custom project routes to override default frontend routes.
        if (file_exists(ROOT_DIR . 'config/routes.php')) {
            require ROOT_DIR . 'config/routes.php';
        }

        // Load frontend routes
        require ROOT_DIR . 'vendor/pitoncms/engine/config/routes.php';

        // If in production, use router cache
        if ($config['environment']['production'] === true) {
            $routeCollector = $app->getRouteCollector();
            $routeCollector->setCacheFile(ROOT_DIR . 'cache/routerCache.php');
        }

        return $app;
    }
);
