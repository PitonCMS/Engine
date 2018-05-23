<?php
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

        // Create the application
        $app = new Slim\App(['settings' => $config]);

        // Load dependencies
        require ROOT_DIR . 'vendor/pitoncms/engine/config/dependencies.php';

        // Load middleware

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
