<?php
// Dependency Injection Container (DIC) Configuration

$container = $app->getContainer();

// Twig templates
$container['view'] = function ($c) {
    // Array of directories to look for templates, in order of priority
    $templatePaths = [
        ROOT_DIR . 'templates/',
        'admin' => ROOT_DIR . 'vendor/pitoncms/engine/templates/admin/',
    ];

    $view = new Slim\Views\Twig($templatePaths, [
        'cache' => ROOT_DIR . 'twigcache',
        'debug' => !$c->get('settings')['production'],
        'autoescape' => false,
    ]);

    // Custom Twig Extensions
    $view->addExtension(new Piton\Extensions\TwigExtension($c));

    // Load Twig debugger if in development
    if ($c->get('settings')['production'] === false) {
        $view->addExtension(new Twig_Extension_Debug());
    }

    return $view;
};

// Monolog logging
$container['logger'] = function ($c) {
    $level = ($c->get('settings')['production']) ? Monolog\Logger::ERROR : Monolog\Logger::DEBUG;
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
    return new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
};

// Custom error handling (overwrite Slim errorHandler to add logging)
$container['errorHandler'] = function ($c) {
    return new Piton\Library\ErrorHandler($c->get('settings')['displayErrorDetails'], $c['logger']);
};

// Sessions
$container['sessionHandler'] = function ($c) {
    return new WolfMoritz\Session\SessionHandler($c['database'], $c->get('settings')['session']);
};

// Security
$container['securityHandler'] = function ($c) {
    return new Piton\Library\SecurityHandler($c->get('sessionHandler'));
};

// Override the default Slim Not Found Handler
$container['notFoundHandler'] = function ($c) {
    return new Piton\Library\NotFoundHandler($c->get('view'), $c->get('logger'));
};

// Emailer
$container['emailHandler'] = function ($c) {
    return new SimpleMail();
};

// Data mapper to CRUD the database tables
$container['dataMapper'] = function ($c) {
    return function ($mapper) use ($c) {
        // Get session user ID
        $session = $c->sessionHandler;
        $userId = ($session->getData('user_id')) ? $session->getData('user_id') : 1;

        // Return instantiated mapper
        $fqn = 'Piton\\Models\\' . $mapper;
        return new $fqn($c['database'], $c['logger'], ['user_id' => $userId]);
    };
};

// Markdown parser
$container['markdownParser'] = function ($c) {
    return new Parsedown();
};
