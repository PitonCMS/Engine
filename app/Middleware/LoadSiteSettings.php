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
        $dataMapper = $this->dataMapper;
        $SettingMapper = $dataMapper('SettingMapper');

        $siteSettings = $SettingMapper->find();

        // Create new multi-dimensional array keyed by the setting category and key
        $this->settings = array_column($siteSettings, 'setting_value', 'setting_key');
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
        $dataMapper = $this->dataMapper;
        $pageMapper = $dataMapper('pageMapper');

        // Fetch all published pages
        $this->pages = $pageMapper->findPages();

        // Set flag on page for current request
        $url = $request->getUri()->getPath();
        $url = ltrim($url, '/');
        $key = array_search($url, array_column($this->pages, 'slug'));

        if (!empty($key)) {
            $this->pages[$key]->currentPage = true;
        }

        return;
    }
}
