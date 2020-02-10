<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

// Dependency Injection Container (DIC) Configuration

// Twig templates
$container['view'] = function ($c) {
    $settings = $c->get('settings');

    // Array of directories for templates, in order of priority
    $templatePaths[] = ROOT_DIR . 'structure/templates/';
    $templatePaths['admin'] = ROOT_DIR . 'vendor/pitoncms/engine/templates/';

    $view = new Slim\Views\Twig($templatePaths, [
        'cache' => ROOT_DIR . 'cache/twig',
        'debug' => !$settings['site']['production'],
        'autoescape' => false,
    ]);

    // Piton Twig Extensions
    $currentPath = $c->request->getUri()->getPath();
    if (substr($currentPath, 0, 6) === '/admin') {
        $view->addExtension(new Piton\Library\Twig\Admin($c));
    } else {
        $view->addExtension(new Piton\Library\Twig\Front($c));
    }

    // Set twig default date filter/function format based on site settings
    // Map site setting date format to PHP equivalent
    $dateFormats = [
        'mm/dd/yyyy' => 'm/d/Y',
        'dd-mm-yyyy' => 'd-m-Y',
        'dd.mm.yyyy' => 'd.m.Y'
        ];

    if (isset($settings['site']['dateFormat'])) {
        $twigEnvironment = $view->getEnvironment();
        $twigEnvironment->getExtension(Twig\Extension\CoreExtension::class)->setDateFormat($dateFormats[$settings['site']['dateFormat']]);
    }

    // Load Twig debugger if in development
    if ($settings['site']['production'] === false) {
        $view->addExtension(new Twig\Extension\DebugExtension());
    }

    return $view;
};

// Monolog logging
$container['logger'] = function ($c) {
    $level = ($c->get('settings')['site']['production']) ? Monolog\Logger::ERROR : Monolog\Logger::DEBUG;
    $logger = new Monolog\Logger('app');
    $logger->pushHandler(new Monolog\Handler\StreamHandler(ROOT_DIR . 'logs/' . date('Y-m-d') . '.log', $level));

    return $logger;
};

// Database connection
$container['database'] = function ($c) {
    $dbConfig = $c->get('settings')['database'];

    // Extra database options
    $dbConfig['options'][PDO::ATTR_PERSISTENT] = true;
    $dbConfig['options'][PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $dbConfig['options'][PDO::ATTR_EMULATE_PREPARES] = false;

    // Define connection string
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4";

    // Return connection
    return new \PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
};

// Custom error exception handling
$container['errorHandler'] = function ($c) {
    return new Piton\Library\Handlers\Error($c->get('settings')['displayErrorDetails'], $c['logger']);
};

// Custom PHP runtime exception handling reuses the error exception handler
$container['phpErrorHandler'] = function ($c) {
    return $c->errorHandler;
};

// Sessions
$container['sessionHandler'] = function ($c) {
    return new Piton\Library\Handlers\Session($c['database'], $c->get('settings')['session']);
};

// Access Control
$container['accessHandler'] = function ($c) {
    return new Piton\Library\Handlers\Access($c->get('sessionHandler'));
};

// Piton route strategy
// https://www.slimframework.com/docs/v3/objects/router.html#route-strategies
$container['foundHandler'] = function ($c) {
    return new Piton\Library\Handlers\RouteArgumentStrategy();
};

// Override the default Slim Not Found handler
$container['notFoundHandler'] = function ($c) {
    return new Piton\Library\Handlers\NotFound($c->get('view'), $c->get('logger'));
};

// Emails
$container['emailHandler'] = function ($c) {
    return new Piton\Library\Handlers\Email(
        new PHPMailer\PHPMailer\PHPMailer(true),
        $c->get('logger'),
        $c->get('settings')
    );
};

// Data mapper to CRUD the database tables
$container['dataMapper'] = function ($c) {
    return function ($mapper) use ($c) {
        // Get session user ID
        $session = $c->sessionHandler;
        $dmConfig['sessionUserId'] = $session->getData('user_id');
        $dmConfig['logger'] = $c['logger'];

        // Return instantiated mapper
        $fqn = 'Piton\\Models\\' . $mapper;
        return new $fqn($c['database'], $dmConfig);
    };
};

// Markdown parser
$container['markdownParser'] = function ($c) {
    return new Piton\Library\Utilities\MDParse();
};

// Definition handler
$container['definition'] = function ($c) {
    return new Piton\Library\Handlers\Definition($c->jsonValidator);
};

// JSON Validator Dependencies for justinrainbow/json-schema
$container['jsonValidator'] = function ($c) {
    return new JsonSchema\Validator();
};

// Piton Toolbox
$container['toolbox'] = function ($c) {
    return new Piton\Library\Utilities\Toolbox();
};

// Piton CSRF Guard
$container['csrfGuard'] = function ($c) {
    return new Piton\Library\Handlers\CsrfGuard($c->sessionHandler, $c->logger);
};

// File Upload Handler
$container['fileUploadHandler'] = function ($c) {
    return new Piton\Library\Handlers\FileUpload($c['request']->getUploadedFiles(), $c['mediaUri']);
};

// Media File Uri
$container['mediaUri'] = function ($c) {
    return function ($fileName) {
        $directory = pathinfo($fileName, PATHINFO_FILENAME);
        $dir = mb_substr($directory, 0, 2);

        return "/media/$dir/$directory/";
    };
};

// Media Handler
$container['mediaHandler'] = function ($c) {
    return new Piton\Library\Handlers\Media($c['mediaUri'], $c['mediaSizes'], $c['settings']['site']['tinifyApiKey']);
};

// Media Size File Suffix Reference
$container['mediaSizes'] = function ($c) {
    $sizes = [
        'original' => '',
        'xlarge' => '-xlarge',
        'large' => '-large',
        'small' => '-small',
        'thumb' => '-thumb'
    ];
    return function ($size = 'original') use ($sizes) {
        if (isset($sizes[$size])) {
            return $sizes[$size];
        }
        return '';
    };
};

// Sitemap Handler
$container['sitemapHandler'] = function ($c) {
    return new Piton\Library\Handlers\Sitemap($c['logger']);
};
