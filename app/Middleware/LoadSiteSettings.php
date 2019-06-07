<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Middleware;

use Interop\Container\ContainerInterface;

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
     * @var Array
     */
    protected $pages;

    /**
     * Constructor
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  closure
     * @param  ArrayAccess
     * @return void
     */
    public function __construct($request, $dataMapper, $appSettings)
    {
        $this->dataMapper = $dataMapper;
        $this->appSettings = $appSettings;
        $this->loadSettings();
        $this->loadPages($request);
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
        // This is a bit of a Slim hack. This is the only $request object that actually has a route object attribute
        // Because of PSR7 immutability the $request object passed into the controller constructor is a copy
        // and does not have the route object attribute
        $route = $request->getAttribute('route');
        $this->settings['currentRouteName'] = ($route !== null) ? $route->getName() : null;

        // Replace site settings with new settings
        $this->appSettings->replace([
            'site' => $this->settings,
            'pages' => $this->pages,
        ]);

        // Next Middleware call
        return $next($request, $response);
    }

    /**
     * Load settings from DB
     *
     * @param none
     * @return void
     */
    public function loadSettings()
    {
        $SettingMapper = ($this->dataMapper)('SettingMapper');
        $siteSettings = $SettingMapper->findSiteSettings();

        // Create new multi-dimensional array keyed by the setting category and key
        $this->settings = array_column($siteSettings, 'setting_value', 'setting_key');

        // Load some config file settings into new settings
        $this->settings['production'] = $this->appSettings['site']['production'];
        $this->settings['pitonDev'] = isset($this->appSettings['site']['pitonDev']) ? $this->appSettings['site']['pitonDev'] : false;
    }

    /**
     * Load pages from DB
     *
     * Sets currentPage = true flag on page record for current request
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @return void
     */
    public function loadPages($request)
    {
        $pageMapper = ($this->dataMapper)('pageMapper');

        // Fetch all published pages
        $this->pages = $pageMapper->findPages();

        // Set flag on page for current request
        $url = $request->getUri()->getPath();
        // Check if home page '/' to match slug name
        $url = ($url === '/') ? 'home' : ltrim($url, '/');
        $key = array_search($url, array_column($this->pages, 'page_slug'));

        if (is_numeric($key)) {
            $this->pages[$key]->currentPage = true;
        }

        return;
    }
}
