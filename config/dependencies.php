<?php
// Dependency Injection Container (DIC) Configuration

// Twig templates
$container['view'] = function ($c) {
    $settings = $c->get('settings');

    // Array of directories for templates, in order of priority, starting with custom theme
    $templatePaths = [];
    if (isset($settings['site']['theme'])) {
        $theme = $settings['site']['theme'];

        if (is_dir(ROOT_DIR . 'themes/' . $theme . '/templates/')) {
            $templatePaths[] = ROOT_DIR . 'themes/' . $theme . '/templates/';
        }
    }

    // Add other template directories
    $templatePaths[] = ROOT_DIR . 'themes/default/templates/';
    $templatePaths['admin'] = ROOT_DIR . 'vendor/pitoncms/engine/templates/';

    $view = new Slim\Views\Twig($templatePaths, [
        'cache' => ROOT_DIR . 'twigcache',
        'debug' => !$settings['production'],
        'autoescape' => false,
    ]);

    // Piton Twig Extensions
    // TODO Is this a useless optimization to separate backend from frontend Twig extensions?
    $currentPath = $c->request->getUri()->getPath();
    if (substr($currentPath, 0, 6) === '/admin') {
        $view->addExtension(new Piton\Library\Twig\Admin($c));
    } else {
        $view->addExtension(new Piton\Library\Twig\Front($c));
    }

    // Load Twig debugger if in development
    if ($settings['production'] === false) {
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
    return new \PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
};

// Custom error handling (overwrite Slim errorHandler to add logging)
$container['errorHandler'] = function ($c) {
    return new Piton\Library\Handlers\Error($c->get('settings')['displayErrorDetails'], $c['logger']);
};

// Sessions
$container['sessionHandler'] = function ($c) {
    return new Piton\Session\SessionHandler($c['database'], $c->get('settings')['session']);
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
    return new Piton\Library\Utilities\MDParse();
};

// Page Layout JSON
$container['pageLayoutJson'] = function ($c) {
    return new Piton\Library\Utilities\PageJson($c);
};
