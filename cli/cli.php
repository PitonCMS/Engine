<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2020 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

use Pimple\Container;
use Piton\CLI\OptimizeMedia;

/**
 * This script accepts PitonCMS command line requests
 * The first argument should be the request to execute, the rest are request arguments.
 * Example:
 * 	$ php cli.php updatesitemap arg1 arg2 arg3
 */

 // Exit if not a Command Line Interface request
if (PHP_SAPI !== 'cli') {
    exit;
}

// Show all errors
error_reporting(-1);

 // Set encoding
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Define the application root directory
define('ROOT_DIR', dirname(__DIR__, 4) . '/');

// Load the Composer Autoloader
require ROOT_DIR . 'vendor/autoload.php';

// Load default and local configuration settings
require ROOT_DIR . 'config/config.default.php';

if (file_exists(ROOT_DIR . 'config/config.local.php')) {
    require ROOT_DIR . 'config/config.local.php';
} else {
    throw new \Exception("PitonCMS: No local configuration file found");
}

// Create container
$container = new Container();

// Load normal dependencies
require ROOT_DIR . 'vendor/pitoncms/engine/config/dependencies.php';

// Override some dependencies for the CLI environment
$container['errorHandler'] = function ($c) {
    print('error');
    exit(1);
};

$container['sessionHandler'] = function ($c) {
    return new class {
        // Spoof user ID
        public function getData(string $key)
        {
            if ($key === 'user_id') {
                return 1;
            }
        }
    };
};

// Parse request
$argv = $GLOBALS['argv'];
array_shift($argv);

if ($argv[0] === 'optimizeMedia') {
    echo "Running optimization...\n";

    $optimizer = new OptimizeMedia($container);
}


echo "End\n";
exit(0);
