<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

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

// Wrap bootstraping code in an anonymous function to avoid unnecessary globals
return call_user_func(
    function () {

        // Load default and local configuration settings
        require ROOT_DIR . 'config/config.default.php';

        if (file_exists(ROOT_DIR . 'config/config.local.php')) {
            require ROOT_DIR . 'config/config.local.php';
        } else {
            throw new \Exception("No local configuration file found");
        }

        // Set error reporting level based on environment
        if ($config['production'] === true) {
            ini_set('display_errors', 'Off');
            error_reporting(0);
            $config['displayErrorDetails'] = false;
        } else {
            // Development
            error_reporting(-1);
            $config['displayErrorDetails'] = true;
        }

        // Create the Slim application and get container
        $app = new Slim\App(['settings' => $config]);
        $container = $app->getContainer();

        // Load dependencies
        require ROOT_DIR . 'vendor/pitoncms/engine/config/dependencies.php';

        // Load dependencies site override
        if (file_exists(ROOT_DIR . 'config/dependencies.php')) {
            require ROOT_DIR . 'config/dependencies.php';
        }

        // Load middleware
        require ROOT_DIR . 'vendor/pitoncms/engine/config/middleware.php';

        // Load admin routes
        require ROOT_DIR . 'vendor/pitoncms/engine/config/routesAdmin.php';

        // Load custom static routes
        if (file_exists(ROOT_DIR . 'config/routes.php')) {
            require ROOT_DIR . 'config/routes.php';
        }

        // Load seeded public routes
        require ROOT_DIR . 'vendor/pitoncms/engine/config/routes.php';

        return $app;
    }
);
