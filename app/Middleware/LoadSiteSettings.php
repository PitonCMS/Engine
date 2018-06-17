<?php
/*
 * Load site settings from database into Container
 */
namespace Piton\Middleware;

use Interop\Container\ContainerInterface;

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
     * @param closure
     * @param ArrayAccess
     */
    public function __construct($dataMapper, $appSettings)
    {
        $this->dataMapper = $dataMapper;
        $this->appSettings = $appSettings;
        $this->loadSettings();
        $this->loadPages();
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
        foreach ($siteSettings as $row) {
            $this->settings[$row->setting_key] = $row->setting_value;
        }
    }

    /**
     * Load pages from DB
     *
     * @param none
     * @return void
     */
    public function loadPages()
    {
        $dataMapper = $this->dataMapper;
        $PageMapper = $dataMapper('PageMapper');

        // Fetch all settings
        $pages = $PageMapper->find();

        // Build pages array with necessary data
        foreach ($pages as $row) {
            $this->pages[$row->name] = [
                'id' => $row->id,
                'name' => $row->name,
                'title' =>$row->title,
                'url' =>$row->url,
                'meta_description' =>$row->meta_description,
            ];
        }
    }
}
