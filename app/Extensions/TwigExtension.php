<?php
/**
 * Custom Piton Extensions for Twig
 */
namespace Piton\Extensions;

use Interop\Container\ContainerInterface;

class TwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var string|\Slim\Http\Uri
     */
    private $uri;

    /**
     * @var Interop\Container\ContainerInterface
     */
    private $container;

    /**
     * @var Array
     */
    private $sitePages;

    /**
     * @var Array
     */
    private $siteSettings;

    /**
     * Constructor
     *
     * @param obj Interop\Container\ContainerInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->uri = $container->request->getUri();
        $this->container = $container;
        $this->sitePages = isset($container['settings']['pages']) ? $container['settings']['pages'] : null;
        $this->siteSettings = isset($container['settings']['site']) ? $container['settings']['site'] : null;
    }

    // Identifer
    public function getName()
    {
        return 'Piton';
    }

    /**
     * Register Global variables
     */
    public function getGlobals()
    {
        return [
            'site' => [
                'pages' => $this->sitePages,
                'settings' => $this->siteSettings,
            ],
        ];
    }

    /**
     * Register Custom Filters
     */
    public function getFilters()
    {
        return [];
    }

    /**
     * Register Custom Functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('pathFor', array($this, 'pathFor')),
            new \Twig_SimpleFunction('baseUrl', array($this, 'baseUrl')),
            new \Twig_SimpleFunction('basePath', array($this, 'basePath')),
            new \Twig_SimpleFunction('inUrl', array($this, 'inUrl')),
            new \Twig_SimpleFunction('checked', array($this, 'checked')),
        ];
    }

    /**
     * Get Path for Named Route
     *
     * @param string $name Name of the route
     * @param array $data Associative array to assign to route segments
     * @param array $queryParams Query string parameters
     * @return string The desired route path without the domain, but does include the basePath
     */
    public function pathFor($name, $data = [], $queryParams = [])
    {
        // The `pathfor('showPage', {'url': 'home'})` route should be an alias for `pathFor('home')`
        if ($name === 'showPage' && isset($data['url']) && $data['url'] === 'home') {
            $name = 'home';
            unset($data['url']);
        }

        return $this->container->router->pathFor($name, $data, $queryParams);
    }

    /**
     * Base URL
     *
     * Returns the base url including scheme, domain, port, and base path
     * @param none
     * @return string The base url
     */
    public function baseUrl()
    {
        if (is_string($this->uri)) {
            return $this->uri;
        }

        return $this->uri->getBaseUrl();
    }

    /**
     * Base Path
     *
     * If the application is run from a directory below the project root
     * this will return the subdirectory path.
     * Use this instead of baseUrl to use relative URL's instead of absolute
     * @param void
     * @return string The base path segments
     */
    public function basePath()
    {
        return $this->uri->getBasePath();
    }

    /**
     * In URL
     *
     * Checks if the supplied string is one of the URL segments
     * @param string $segment URL segment to find
     * @return boolean
     */
    public function inUrl($segmentToTest = null)
    {
        // Verify we have a segment to find
        if ($segmentToTest === null) {
            return false;
        }

        // If just a slash is provided, meaning 'home', then evaluate
        if ($segmentToTest === '/' && ($this->uri->getPath() === '/' || empty($this->uri->getPath()))) {
            return true;
        } elseif ($segmentToTest === '/' && !empty($this->uri->getPath())) {
            return false;
        }

        // Clean segment of slashes
        $segmentToTest = trim($segmentToTest, '/');

        return in_array($segmentToTest, explode('/', $this->uri->getPath()));
    }

    /**
     * Set Checkbox
     *
     * If the supplied value is truthy, 1, or 'Y' returns the checked string
     * @param mixed $value
     * @return string
     */
    public function checked($value = 0)
    {
        return ($value == 1 || $value == 'Y') ? 'checked="checked"' : '';
    }
}
