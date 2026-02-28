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
 * Dependency Injection Container (DIC) Configuration
 *
 * Override any container entry in config/dependencies.php
 */

use Piton\Library\Config;
use Piton\Library\Twig\Base;
use Piton\Pagination\TwigPagination;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Extension\DebugExtension;

/**
 * Config Settings Value Object
 *
 * @uses array $config
 * @return Piton\Library\Config
 */
$container->set('settings', function () use ($config) {
    return new Config($config);
});

/**
 * Router
 *
 * This loads Slim route parser
 * @uses Slim/App $app
 * @return Slim\Routing\RouteParser
 */
$container->set('router', function () use ($app) {
    return $app->getRouteCollector()->getRouteParser();
});

/**
 * Get PSR Response Factory
 *
 * Used to create Response objects
 * @param void
 * @return ResponseFactory
 */
$container->set('responseFactory', function () {
    return new ResponseFactory();
});

/**
 * Twig HTML Templates
 *
 * Loads
 * - Template directories
 * - Debug setting
 * - Adds as middleware to Slim App
 *
 * Note: the Piton Twig packages (Base & Pagination) are loaded by the BaseController::pitonViewExtensions(),
 * which are needed by the CMS
 *
 * @param ContainerInterface $c
 * @uses Slim/App $app
 * @return Slim\Views\Twig
 */
$container->set('view', function (ContainerInterface $c) use ($app) {
    $settings = $c->get('settings');

    // Array of directories for templates
    $templatePaths[] = ROOT_DIR . 'structure/templates/';
    $templatePaths['admin'] = ROOT_DIR . 'vendor/pitoncms/engine/templates/';

    $view = Twig::create($templatePaths, [
        'cache' => ROOT_DIR . 'cache/twig',
        'debug' => !$settings['environment']['production'],
        'autoescape' => false,
    ]);

    // Load Twig debugger if in development
    if (!$settings['environment']['production']) {
        $view->addExtension(new DebugExtension());
    }

    // Piton Twig Extension
    $view->addExtension($c->get('TwigBaseExtension'));

    // Load Pagination with default results per page setting
    $view->addExtension(new TwigPagination(['resultsPerPage' => $c->get('settings')['pagination']['resultsPerPage']]));

    // Add to $app per Slim 4
    $app->add(TwigMiddleware::create($app, $view));

    return $view;
});

/**
 * Twig Base Extension
 *
 * Registers the custom twig extension Base, so that we can hydrate it in an early middleware call with Request URI primitive data
 * @return Base
 */
$container->set('TwigBaseExtension', function (ContainerInterface $c) {
    return new Base($c);
});

/**
 * Monolog PSR3 Logger
 *
 * If production minimum log level is ERRROR, but if not then all are logged.
 * Logging Levels:
 *  - DEBUG - detailed debug information
 *  - INFO - interesting events
 *  - NOTICE - normal but significant events
 *  - WARNING - exceptional occurrences that are not errors
 *  - ERROR - runtime errors that do not require immediate action
 *  - CRITICAL - critical conditions
 *  - ALERT - events for which action must be taken immediately
 *  - EMERGENCY - emergency events
 *
 * @param ContainerInterface $c
 * @return Monolog\Logger
 */
$container->set('logger', function (ContainerInterface $c) {
    $level = ($c->get('settings')['environment']['production']) ? Monolog\Logger::ERROR : Monolog\Logger::DEBUG;
    $logger = new Monolog\Logger('app');
    $logger->pushHandler(new Monolog\Handler\StreamHandler(ROOT_DIR . 'logs/' . date('Y-m-d') . '.log', $level));

    return $logger;
});

/**
 * Database Connection
 *
 * @param ContainerInterface $c
 * @return PDO
 */
$container->set('database', function (ContainerInterface $c) {
    $dbConfig = $c->get('settings')['database'];

    // Extra database options
    $dbConfig['options'][PDO::ATTR_PERSISTENT] = true;
    $dbConfig['options'][PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $dbConfig['options'][PDO::ATTR_EMULATE_PREPARES] = false;

    // Define connection string
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4";

    // Return connection
    return new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
});

/**
 * Exception HTML Renderer
 *
 * For 5xx Exceptions, displays PitonCMS error template
 * @param void
 * @return ErrorRenderer
 */
$container->set('ErrorRenderer', function () {
    return new Piton\Library\Handlers\ErrorRenderer();
});

/**
 * Session Handler
 *
 * Manages session state.
 * @param ContainerInterface $c
 * @return Piton\Library\Handlers\Session
 */
$container->set('sessionHandler', function (ContainerInterface $c) {
    $settings = $c->get('settings');
    $sessionConfig = $settings['session'];
    $sessionConfig['domain'] = $settings['environment']['domain'] ?? '';

    return new Piton\Library\Handlers\Session($c->get('database'), $sessionConfig, $c->get('logger'));
});

/**
 * Access Handler
 *
 * Handler for user access control
 * @param ContainerInterface $c
 * @return Piton\Library\Handlers\Access
 */
$container->set('accessHandler', function (ContainerInterface $c) {
    return new Piton\Library\Handlers\Access($c->get('sessionHandler'));
});

/**
 * Not Found (404)
 *
 * Handles not found exceptions
 * @param ContainerInterface $c
 * @return Piton\Library\Handlers\NotFound
 */
$container->set('notFoundHandler', function (ContainerInterface $c) {
    return new Piton\Library\Handlers\NotFound($c->get('responseFactory'), $c->get('view'));
});

/**
 * Email Handler
 *
 * @param ContainerInterface $c
 * @return Piton\Library\Handlers\Email
 */
$container->set('emailHandler', function (ContainerInterface $c) {
    return new Piton\Library\Handlers\Email(
        new PHPMailer\PHPMailer\PHPMailer(true),
        $c->get('logger'),
        $c->get('settings')
    );
});

/**
 * Data Mapper Closure
 *
 * Data mapper ORM to CRUD the database tables
 * Returns closure to request DB table data mapper object
 * @param ContainerInterface $c
 * @return closure
 */
$container->set('dataMapper', function (ContainerInterface $c) {
    /**
     * Data Mapper
     *
     * Returns instance of the requested data mapper
     * @param string  $mapper    Class name of mapper
     * @param string  $namesapce Optional namespace. Defaults to Piton\\Models\\. Escape backslashes!
     * @return object            Data mapper ORM
     */
    return function (string $mapper, string $namepace = 'Piton\\Models\\') use ($c) {
        // Construct fully qualified domain name class to instantiate
        $fqn = $namepace . $mapper;

        // Load session user ID to set update column, and provide PSR3 logger
        $session = $c->get('sessionHandler');
        $options['sessionUserId'] = (int) $session->getData('user_id') ?? 0;

        // Pass in logger and default value object class.
        $options['logger'] = $c->get('logger');

        // Return instantiated mapper with DB connection and options
        return new $fqn($c->get('database'), $options);
    };
});

/**
 * Markdown Parser
 *
 * @param void
 * @return League\CommonMark\GithubFlavoredMarkdownConverter
 */
$container->set('markdownParser', function () {
    return new \League\CommonMark\GithubFlavoredMarkdownConverter();
});

/**
 * JSON Definition Handler
 *
 * @param ContainerInterface $c
 * @return Piton\Library\Handlers\Definition
 */
$container->set('jsonDefinitionHandler', function (ContainerInterface $c) {
    return new Piton\Library\Handlers\Definition($c->get('jsonValidator'));
});

/**
 * JSON Validation
 *
 * @param void
 * @return JsonSchema\Validator
 */
$container->set('jsonValidator', function () {
    return new JsonSchema\Validator();
});

/**
 * Misc Utility Toolbox
 *
 * Piton toolbox has various utility methods
 * @param void
 * @return Piton\Library\Utilities\Toolbox
 */
$container->set('toolbox', function () {
    return new Piton\Library\Utilities\Toolbox();
});

/**
 * CSRF Guard Handler
 *
 * Checks submitted CSRF token on POST requests
 * @param ContainerInterface $c
 * @return Piton\Library\Handlers\CsrfGuard
 */
$container->set('csrfGuardHandler', function (ContainerInterface $c) {
    return new Piton\Library\Handlers\CsrfGuard($c->get('sessionHandler'), $c->get('responseFactory'), $c->get('logger'));
});

/**
 * Sitemap Handler
 *
 * Creates XML sitemap based on saved pages
 * @param ContainerInterface $c
 * @return Piton\Library\Handlers\Sitemap
 */
$container->set('sitemapHandler', function (ContainerInterface $c) {
    return new Piton\Library\Handlers\Sitemap($c->get('logger'));
});

/**
 * File Upload Handler
 *
 * Manages file uploads.
 * Renames uploaded files and places in the directory defined in the mediaPathHandler
 * @param ContainerInterface $c
 * @return Piton\Library\Handlers\FileUpload
 */
$container->set('fileUploadHandler', function (ContainerInterface $c) {
    return new Piton\Library\Handlers\FileUpload($c->get('mediaPathHandler'), $c->get('filenameGenerator'));
});

/**
 * Media File Path Pattern Handler
 *
 * Define upload media path under public/media/
 * Can be overriden with custom public path
 * @param void
 * @return string
 */
$container->set('mediaPathHandler', function () {
    return function ($fileName) {
        $directory = pathinfo($fileName, PATHINFO_FILENAME);
        $dir = mb_substr($directory, 0, 2);

        return "/media/$dir/$directory/";
    };
});

/**
 * Media Handler
 *
 * Resizes and optimizes media using a TinyJPG key
 * @param ContainerInterface $c
 * @return Piton\Library\Handlers\Media
 */
$container->set('mediaHandler', function (ContainerInterface $c) {
    return new Piton\Library\Handlers\Media($c->get('mediaPathHandler'), $c->get('mediaSizes'), $c->get('settings')['site']['tinifyApiKey']);
});

/**
 * Media Image Size List
 *
 * List of image size suffixes.
 * Used as validation and to construct alternate source sets.
 * @param void
 * @return array
 */
$container->set('mediaSizeList', function () {
    return ['xlarge', 'large', 'small', 'thumb'];
});

/**
 * Media Size Constructor
 *
 * Given a filename and a desired size, checks the size against mediaSizes and then returns
 * desired filename with size.
 * @param ContainerInterface $c
 * @return string
 */
$container->set('mediaSizes', function (ContainerInterface $c) {
    return function ($filename, $size = '') use ($c) {
        if (in_array($size, $c->get('mediaSizeList'))) {
            $parts = pathinfo($filename);

            return "{$parts['filename']}-$size.{$parts['extension']}";
        }

        // If not a listed size, just return the filename as-is
        return $filename;
    };
});

/**
 * Media Filename Generator
 *
 * Creates new filename for uploaded files
 * @param void
 * @return string
 */
$container->set('filenameGenerator', function () {
    return function () {
        return bin2hex(random_bytes(6));
    };
});
