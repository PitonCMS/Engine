<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2020 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Library\Twig;

use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;
use FilesystemIterator;

/**
 * Piton Base Twig Extension
 *
 * Has core Twig properties and functions used on public and on admin sites.
 */
class Base extends AbstractExtension implements GlobalsInterface
{
    /**
     * Cache
     * @var array
     */
    protected $cache = [];

    /**
     * @var Slim\Http\Uri
     */
    protected $uri;

    /**
     * @var Psr\Container\ContainerInterface
     */
    protected $container;

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
     * @param object Psr\Container\ContainerInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->uri = $container->request->getUri();

        $this->csrfTokenName = ($container->csrfGuardHandler)->getTokenName();
        $this->csrfTokenValue = ($container->csrfGuardHandler)->getTokenValue();
    }

    /**
     * Register Global variables
     */
    public function getGlobals(): array
    {
        return [
            'site' => [
                'settings' => $this->container['settings']['site'] ?? null,
                'environment' => $this->container['settings']['environment'] ?? null,
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
    public function getFilters(): array
    {
        return [];
    }

    /**
     * Register Custom Functions
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('pathFor', [$this, 'pathFor']),
            new TwigFunction('baseUrl', [$this, 'baseUrl']),
            new TwigFunction('basePath', [$this, 'basePath']),
            new TwigFunction('currentRoute', [$this, 'currentRoute']),
            new TwigFunction('inUrl', [$this, 'inUrl']),
            new TwigFunction('checked', [$this, 'checked']),
            new TwigFunction('getMediaPath', [$this, 'getMediaPath']),
            new TwigFunction('getMediaSrcSet', [$this, 'getMediaSrcSet']),
            new TwigFunction('getQueryParam', [$this, 'getQueryParam']),
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
    public function pathFor(string $name, array $data = [], array $queryParams = []): string
    {
        // The `pathfor('showPage', {'url': 'home'})` route should be an alias for `pathFor('home')`
        if ($name === 'showPage' && isset($data['slug1']) && $data['slug1'] === 'home') {
            $name = 'home';
            unset($data['url']);
        }

        return $this->container->router->pathFor($name, $data, $queryParams);
    }

    /**
     * Base URL
     *
     * Returns the base url including scheme, domain, port, and base path
     * @param void
     * @return string The base url
     */
    public function baseUrl(): string
    {
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
    public function basePath(): string
    {
        return $this->uri->getBasePath();
    }

    /**
     * Current Route
     *
     * If the supplied route name is the current route, returns the second parameter
     * @param  string $routeName   Name of the route to test
     * @param  string $returnValue Value to return
     * @return string|null
     */
    public function currentRoute(string $routeName, string $returnValue = 'active'): ?string
    {
        if ($routeName === $this->container->settings['environment']['currentRouteName']) {
            return $returnValue;
        }

        return null;
    }

    /**
     * In URL
     *
     * Checks if the supplied string is one of the current URL segments
     * @param string  $segment       URL segment to find
     * @param string  $valueToReturn Value to return if true
     * @return string|null           Returns $valueToReturn or null
     */
    public function inUrl(string $segmentToTest = null, $valueToReturn = 'active'): ?string
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
     * @return string|null
     */
    public function checked($value = 0): ?string
    {
        //      ------------------------- Exactly True ------------------------------| Truthy Fallback
        return ($value === 'Y' || $value === 1 || $value === true || $value === 'on' || $value == 1) ? 'checked' : null;
    }

    /**
     * Get Media Path
     *
     * @param  string $filename Media file name to parse
     * @param  string $size     Media size: original|xlarge|large|small|thumb
     * @return string
     */
    public function getMediaPath(?string $filename, string $size = 'original'): ?string
    {
        // Return nothing if no filename was provided
        if (empty($filename)) {
            return null;
        }

        // If this is an external link to a file, just return
        if (mb_stripos($filename, 'http') === 0) {
            return $filename;
        }

        // If the original is requested, return path and filename
        if ($size === 'original') {
            return ($this->container->mediaPathHandler)($filename) . $filename;
        }

        // Construct path and requested file size, and if file exists then return
        $media = ($this->container->mediaPathHandler)($filename) . ($this->container->mediaSizes)($filename, $size);
        if (file_exists(ROOT_DIR . 'public' . $media)) {
            return $media;
        }

        // Fall back to original file if other size not found
        return ($this->container->mediaPathHandler)($filename) . $filename;
    }

    /**
     * Get Media Source Set
     *
     * Creates list of available image files in source set format
     * @param string $filename Media filename
     * @param string $altText  Media caption to use as alt text
     * @param string $sizes    Source set sizes string
     * @return string
     */
    public function getMediaSrcSet(string $filename = null, string $altText = null, string $sizes = null): ?string
    {
        // If filename is empty, just return nothing
        if (empty($filename)) {
            return null;
        }

        // Get cached img source set for this file if available
        if (isset($this->cache['mediaSrcSet'][$filename])) {
            return $this->cache['mediaSrcSet'][$filename];
        }

        // Get image directory and scan for all sizes
        $imageDir = ($this->container->mediaPathHandler)($filename);
        if (!is_dir(ROOT_DIR . 'public' . $imageDir)) {
            // Something wrong here
            $this->container->logger->warning("Twig Base getMediaSrcSet() directory not found. \$filename: $filename, Looking for: $imageDir");
            return null;
        }
        $files = new FilesystemIterator(ROOT_DIR . 'public' . $imageDir);

        // Create array of available images with actual sizes, sorted by ascending size
        $sources = [];
        foreach ($files as $file) {
            // Include only image variants, not the original or thumb. Thumbnails will be loaded explicity when needed
            if ($filename !== $file->getFilename() && false === mb_strpos($file->getFilename(), 'thumb')) {
                // Only include in source set if width is non-zero (possible error)
                $info = getimagesize($file->getPathname());
                if (is_int($info[0]) && $info[0] > 0) {
                    $sources[$info[0]] = "$imageDir{$file->getFilename()} {$info[0]}w";
                }
            }
        }
        ksort($sources);

        $sourceSet = implode(",\n", $sources);
        $sizes = $sizes ?? '(max-width: 767px) 100vw, (max-width: 899px) 50vw, 33vw';

        // Create HTML source set string only if there is more than one media file
        $sourceSetString = (iterator_count($files) > 1) ? "srcset=\"$sourceSet\"\nsizes=\"$sizes\"\n" : '';
        $srcAttr = $this->getMediaPath($filename, 'xlarge');

        return $this->cache['mediaSrcSet'][$filename] = "<img $sourceSetString src=\"$srcAttr\" alt=\"$altText\">\n";
    }

    /**
     * Get Query String Parameter
     *
     * Returns htmlspecialchars() escaped query param
     * Missing params and empty string values are returned as null
     * @param string|null $param
     * @return string|null
     */
    public function getQueryParam(string $param = null): ?string
    {
        $value = $this->container->request->getQueryParam($param);

        if (!empty($value)) {
            return htmlspecialchars($value);
        }

        return null;
    }
}
