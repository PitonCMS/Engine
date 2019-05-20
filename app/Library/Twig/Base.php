<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Twig;

use Interop\Container\ContainerInterface;

/**
 * Piton Base Twig Extension
 *
 * Has core Twig properties and functions used on public and on admin sites.
 */
class Base extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var string|\Slim\Http\Uri
     */
    protected $uri;

    /**
     * @var Interop\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var Array
     */
    protected $sitePages;

    /**
     * @var Array
     */
    protected $siteSettings;

    /**
     * Piton CSRF Token Name
     * @var string
     */
    protected $csrfTokenName;

    /**
     * Piton CSRF Token Value
     * @var string
     */
    protected $csrfTokenValue;

    /**
     * Constructor
     *
     * @param obj Interop\Container\ContainerInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->uri = $container->request->getUri();
        $this->sitePages = isset($container['settings']['pages']) ? $container['settings']['pages'] : null;
        $this->siteSettings = isset($container['settings']['site']) ? $container['settings']['site'] : null;

        $this->csrfTokenName = ($container->csrfGuard)->getTokenName();
        $this->csrfTokenValue = ($container->csrfGuard)->getTokenValue();
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
                'csrf' => [
                    'name' => $this->csrfTokenName,
                    'value' => $this->csrfTokenValue
                ]
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
            new \Twig_SimpleFunction('pathFor', [$this, 'pathFor']),
            new \Twig_SimpleFunction('baseUrl', [$this, 'baseUrl']),
            new \Twig_SimpleFunction('basePath', [$this, 'basePath']),
            new \Twig_SimpleFunction('currentRoute', [$this, 'currentRoute']),
            new \Twig_SimpleFunction('inUrl', [$this, 'inUrl']),
            new \Twig_SimpleFunction('checked', [$this, 'checked']),
            new \Twig_SimpleFunction('getMediaPath', [$this, 'getMediaPath']),
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
     * Current Route
     *
     * If the supplied route name is the current route, returns the second parameter
     * @param  string $routeName   Name of the route to test
     * @param  mixed  $returnValue Value to return
     * @return mixed
     */
    public function currentRoute($routeName, $returnValue = 'active')
    {
        if ($routeName === $this->container->settings['site']['currentRouteName']) {
            return $returnValue;
        }

        return null;
    }

    /**
     * In URL
     *
     * Checks if the supplied string is one of the current URL segments
     * @param string  $segment       URL segment to find
     * @param mixed   $valueToReturn Value to return if true
     * @return mixed                 Returns $valueToReturn or null
     */
    public function inUrl($segmentToTest = null, $valueToReturn = 'active')
    {
        // Verify we have a segment to find
        if ($segmentToTest === null) {
            return null;
        }

        // If just a slash is provided, meaning 'home', then evaluate
        if ($segmentToTest === '/' && ($this->uri->getPath() === '/' || empty($this->uri->getPath()))) {
            return $valueToReturn;
        } elseif ($segmentToTest === '/' && !empty($this->uri->getPath())) {
            return null;
        }

        // Clean segment of slashes
        $segmentToTest = trim($segmentToTest, '/');

        if (in_array($segmentToTest, explode('/', $this->uri->getPath()))) {
            return $valueToReturn;
        }

        return null;
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
        //      ------------------------- Exactly True ------------------------------| Truthy Fallback
        return ($value === 'Y' || $value === 1 || $value === true || $value === 'on' || $value == 1) ? 'checked' : '';
    }

    /**
     * Get Media Path
     *
     * @param  string $filename Media file name to parse
     * @param  string $size     Media size: original|xlarge|large|thumb
     * @return string
     */
    public function getMediaPath($filename, $size = 'original')
    {
        // If this is an external link to a media file, just return string
        if (mb_stripos($filename, 'http') === 0) {
            return $filename;
        }

        // If the original is requested, pass through the URI and filename
        if ($size === 'original') {
            return ($this->container->mediaUri)($filename) . $filename;
        }

        // Construct requested file URI
        $pathParts = pathinfo($filename);
        $baseUri = ($this->container->mediaUri)($filename);
        $requestedSize = $pathParts['filename'] . ($this->container->mediaSizes)($size) . '.' . $pathParts['extension'];

        if (file_exists(ROOT_DIR . 'public' . $baseUri . $requestedSize)) {
            return $baseUri . $requestedSize;
        } else {
            // Fall back is original file
            return $baseUri . $filename;
        }
    }
}
