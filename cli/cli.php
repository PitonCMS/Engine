<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

use DI\Container;
use Piton\CLI\OptimizeMedia;
use Psr\Container\ContainerInterface;

/**
 * This script accepts PitonCMS command line requests
 *
 * The first argument should be the request command to execute, the second is the ROOT_DIR path
 * Example:
 * 	$ php cli.php command root-dir arg2 arg3
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

// Verify that the CLI command has at least two arguments: Request and a ROOT_DIR path
if ($argc < 3) {
    fwrite(STDERR, "Error: Missing required arguments\n");
    fwrite(STDERR, "Usage: php cli.php <command> <root_dir>\n");
    fwrite(STDERR, "Available commands: optimize-media, clitest\n");
    exit(1);
}

// Extract and validate command
$command = $argv[1] ?? '';
$validCommands = ['optimize-media', 'clitest'];

if (!in_array($command, $validCommands, true)) {
    fwrite(STDERR, "Error: Invalid command '$command'\n");
    fwrite(STDERR, "Available commands: " . implode(', ', $validCommands) . "\n");
    exit(1);
}

// Extract and validate root directory
$rootDir = rtrim($argv[2] ?? '', '/') . '/';

if (!is_dir($rootDir)) {
    fwrite(STDERR, "Error: Root directory does not exist: $rootDir\n");
    exit(1);
}

if (!file_exists($rootDir . 'composer.json')) {
    fwrite(STDERR, "Error: Invalid root directory (composer.json not found): $rootDir\n");
    exit(1);
}

// Now good to continue with script

// Define ROOT_DIR constant
define('ROOT_DIR', $rootDir);

// Load the Composer Autoloader and add Piton\CLI namespace
$loader = require ROOT_DIR . 'vendor/autoload.php';
$loader->addPsr4('Piton\\CLI\\', __DIR__);

// Load default and local configuration settings
require ROOT_DIR . 'vendor/pitoncms/engine/config/config.default.php';

if (file_exists(ROOT_DIR . 'config/config.local.php')) {
    require ROOT_DIR . 'config/config.local.php';
} else {
    fwrite(STDERR, "PitonCMS CLI: No local configuration file found");
    exit(1);
}

// Create container
$container = new Container();

// Create an instance of a Slim App, otherwise dependencies.php will throw errors
\Slim\Factory\AppFactory::setContainer($container);
$app = \Slim\Factory\AppFactory::create();

// Load dependencies into container
require ROOT_DIR . 'vendor/pitoncms/engine/config/dependencies.php';

// Override some dependencies for the CLI environment
$container->set('errorHandler', function (ContainerInterface $c) {
    fwrite(STDERR, "Error in Piton cli/cli.php. Exiting.\n");
    exit(1);
});

$container->set('sessionHandler', function (ContainerInterface $c) {
    return new class () {
        // Spoof user ID
        public function getData(string $key)
        {
            if ($key === 'user_id') {
                return 1;
            }
        }
    };
});

// Load saved site settings from data_store and merge into $container settings
$dataStoreMapper = ($container->get('dataMapper'))('DataStoreMapper');
$siteSettings = $dataStoreMapper->findSiteSettings() ?? [];

// Create new multi-dimensional array of 'environment' (piton) and 'site' (other category) settings
$loadSettings = [];
foreach ($siteSettings as $row) {
    if ($row->category === 'piton') {
        $loadSettings['environment'][$row->setting_key] = $row->setting_value;
    } else {
        $loadSettings['site'][$row->setting_key] = $row->setting_value;
    }
}

// Merge saved settings into setting Config
$settings = $container->get('settings');
$settings->merge($loadSettings);

// Execute the command at the end
switch ($command) {
    case 'optimize-media':
        $optimizer = new OptimizeMedia($container);
        $optimizer->run();

        break;
    case 'clitest':
        fwrite(STDERR, "Piton CLI is working\n");
        exit(0);

        break;
}

exit(0);
