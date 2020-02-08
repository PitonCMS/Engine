<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Middleware;

use ArrayAccess;

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
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  closure
     * @param  ArrayAccess
     * @return void
     */
    public function __construct($request, $dataMapper, ArrayAccess $appSettings)
    {
        $this->dataMapper = $dataMapper;
        $this->appSettings = $appSettings;
    }

    /**
     * Callable
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
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
        $settingMapper = ($this->dataMapper)('SettingMapper');
        $siteSettings = $settingMapper->findSiteSettings() ?? [];

        // Create new multi-dimensional array
        $this->settings = array_column($siteSettings, 'setting_value', 'setting_key');

        // Load some config file settings into settings array
        $this->settings['production'] = $this->appSettings['site']['production'];
        $this->settings['pitonDev'] = isset($this->appSettings['site']['pitonDev']) ?: false;
    }
}
