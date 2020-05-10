<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2020 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use ArrayAccess;
use PDOException;

/*
 * Load site settings from database into Container
 */
class LoadSiteSettings
{
    /**
     * @var Closure returning data mapper
     */
    protected $dataMapper;

    /**
     * @var ArrayAccess
     */
    protected $appSettings;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Constructor
     *
     * @param  closure
     * @param  ArrayAccess
     * @return void
     */
    public function __construct($dataMapper, ArrayAccess $appSettings)
    {
        $this->dataMapper = $dataMapper;
        $this->appSettings = $appSettings;
    }

    /**
     * Callable
     *
     * @param  Request  $request  PSR7 request
     * @param  Response $response PSR7 response
     * @param  callable $next     Next middleware
     * @return Response
     */
    public function __invoke($request, $response, $next): Response
    {
        $this->loadSettings();

        // This is a bit of a Slim hack. This is the only $request object that actually has a route object attribute
        // Because of PSR7 immutability the $request object passed into the controller constructor is a copy
        // and does not have the route object attribute
        $route = $request->getAttribute('route');
        $this->settings['currentRouteName'] = ($route !== null) ? $route->getName() : null;

        // Replace site settings with new settings
        $this->appSettings->replace([
            'site' => $this->settings
        ]);

        // Next Middleware call
        return $next($request, $response);
    }

    /**
     * Load settings from DB
     *
     * @param  void
     * @return void
     */
    protected function loadSettings()
    {
        // This middleware data request is the first DB query in the application lifecycle.
        // If the tables do not exist (SQLSTATE[42S02]) catch and redirect to install.php script.
        // Otherwise rethrow to let the application handler deal with whatever happened.
        try {
            $dataStoreMapper = ($this->dataMapper)('DataStoreMapper');
        } catch (PDOException $th) {
            // SQLSTATE[42S02]
            if ($th->getCode() === '42S02') {
                // Go to installer
                header('Location: /install.php', true, 302);
                exit;
            } else {
                throw $th;
            }
        }

        $siteSettings = $dataStoreMapper->findSiteSettings() ?? [];

        // Create new multi-dimensional array
        $this->settings = array_column($siteSettings, 'setting_value', 'setting_key');

        // Load some config file settings into settings array
        $this->settings['production'] = $this->appSettings['site']['production'];
    }
}
