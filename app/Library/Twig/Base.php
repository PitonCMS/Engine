<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Library\Twig;

use Piton\Pagination\TwigPagination;
use Piton\Models\Entities\PitonEntity;
use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\Error\LoaderError;
use Twig\TwigFunction;
use FilesystemIterator;
use Exception;

/**
 * Piton Twig Extension
 *
 * Custom functions used on public and on admin sites.
 */
class Base extends AbstractExtension implements GlobalsInterface
{
    /**
     * Cache
     * @var array
     */
    protected $cache = [];

    /**
     * URI Object
     * @var Slim\Http\Uri
     */
    protected $uri;

    /**
     * Dependency Container
     * @var Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * Admin Site Hierarchy
     *
     * pageRouteName => parentPageRouteName
     * Null values represent top level navigation routes
     * @var array
     */
    protected const AdminSiteHierarchy = [
        // Level 0 pages
        'adminHome' => null,
        'adminPage' => null,
        'adminMedia' => null,
        'adminNavigation' => null,
        'adminMessage' => null,
        'adminSetting' => null,
        'adminSupportIndex' =>  null,

        // Level 1 pages
        'adminPageEdit' => 'adminPage',
        'adminNavigationEdit' => 'adminNavigation',
        'adminSettingEdit' => 'adminSetting',
        'adminSitemap' => 'adminSetting',
        'adminCollection' => 'adminSetting',
        'adminMediaCategoryEdit' => 'adminSetting',
        'adminUser' => 'adminSetting',
        'adminSupportContent' => 'adminSupportIndex',
        'adminSupportAbout' => 'adminSupportIndex',

        // Level 2 pages
        'adminUserEdit' => 'adminUser',
        'adminCollectionEdit' => 'adminCollection',
    ];

    /**
     * Constructor
     *
     * @param object Psr\Container\ContainerInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->uri = $container->request->getUri();
    }

    /**
     * Register Global variables
     *
     * @param void
     * @return array
     */
    public function getGlobals(): array
    {
        return [
            'site' => [
                'settings' => $this->container['settings']['site'],
                'environment' => $this->container['settings']['environment'],
            ],
        ];
    }

    /**
     * Register Custom Filters
     *
     * @param void
     * @return array
     */
    public function getFilters(): array
    {
        return [];
    }

    /**
     * Register Custom Functions
     *
     * @param void
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            // Base functions
            new TwigFunction('pathFor', [$this, 'pathFor']),
            new TwigFunction('baseUrl', [$this, 'baseUrl']),
            new TwigFunction('basePath', [$this, 'basePath']),
            new TwigFunction('currentPath', [$this, 'currentPath']),
            new TwigFunction('currentUrl', [$this, 'currentUrl']),
            new TwigFunction('currentRoute', [$this, 'currentRoute']),
            new TwigFunction('inUrl', [$this, 'inUrl']),
            new TwigFunction('checked', [$this, 'checked']),
            new TwigFunction('getMediaPath', [$this, 'getMediaPath']),
            new TwigFunction('getMediaSrcSet', [$this, 'getMediaSrcSet']),
            new TwigFunction('getQueryParam', [$this, 'getQueryParam']),
            new TwigFunction('truncateHtml', [$this, 'truncateHtml']),

            // Front end functions
            new TwigFunction('getPathForPage', [$this, 'getPathForPage']),
            new TwigFunction('getBlockElementsHtml', [$this, 'getBlockElementsHtml'], ['is_safe' => ['html']]),
            new TwigFunction('getElementHtml', [$this, 'getElementHtml'], ['is_safe' => ['html']]),
            new TwigFunction('getCollectionPages', [$this, 'getCollectionPages']),
            new TwigFunction('getCollectionPagesWithPagination', [$this, 'getCollectionPagesWithPagination']),
            new TwigFunction('getPublishedRankedCollectionPages', [$this, 'getPublishedRankedCollectionPages']),
            new TwigFunction('getGallery', [$this, 'getGallery']),
            new TwigFunction('getNavigator', [$this, 'getNavigator']),
            new TwigFunction('getNavigationLink', [$this, 'getNavigationLink']),
            new TwigFunction('getSearchResultsWithPagination', [$this, 'getSearchResultsWithPagination']),

            // Back end functions
            new TwigFunction('uniqueKey', [$this, 'uniqueKey']),
            new TwigFunction('getAlert', [$this, 'getAlert'], ['needs_context' => true]),
            new TwigFunction('getCollections', [$this, 'getCollections']),
            new TwigFunction('getPageTemplates', [$this, 'getPageTemplates']),
            new TwigFunction('getMediaCategories', [$this, 'getMediaCategories']),
            new TwigFunction('getElements', [$this, 'getElements']),
            new TwigFunction('getUnreadMessageCount', [$this, 'getUnreadMessageCount']),
            new TwigFunction('getSessionData', [$this, 'getSessionData']),
            new TwigFunction('getJsFileSource', [$this, 'getJsFileSource']),
            new TwigFunction('currentRouteParent', [$this, 'currentRouteParent']),
            new TwigFunction('getMaxUploadSize', [$this, 'getMaxUploadSize']),
            new TwigFunction('getJsFileExtensions', [$this, 'getJsFileExtensions']),
        ];
    }

    /**
     * Get Pagination Object
     *
     * Returns Piton\Pagination\TwigPagination object from the Twig environment array of extensions
     * to allow update of runtime settings
     * @param void
     * @return Piton\Pagination\TwigPagination
     */
    protected function getPagination(): TwigPagination
    {
        return $this->container->view->getEnvironment()->getExtensions()['Piton\Pagination\TwigPagination'];
    }

    /**
     * Path for Named Route
     *
     * See getPathForPage() for front end routes to pages.
     * Method to resolve route names to a URL
     * @param string  $name        Name of the route
     * @param array   $data        Associative array to assign to route segments
     * @param array   $queryParams Query string parameters
     * @return string              The desired route path without the domain, but does include the basePath
     */
    public function pathFor(string $name, array $data = [], array $queryParams = []): string
    {
        // The `pathfor('showPage', ['slug1' => 'home'])` route is an alias for `pathFor('home')`
        if ($name === 'showPage' && isset($data['slug1']) && $data['slug1'] === 'home') {
            $name = 'home';
            unset($data['slug1']);
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
     * Returns current path on given URI.
     *
     * @param bool $withQueryString
     * @return string
     */
    public function currentPath($withQueryString = false)
    {
        if (is_string($this->uri)) {
            return $this->uri;
        }

        $path = $this->uri->getBasePath() . '/' . ltrim($this->uri->getPath(), '/');

        if ($withQueryString && '' !== $query = $this->uri->getQuery()) {
            $path .= '?' . $query;
        }

        return $path;
    }

    /**
     * Get Current Complete URL
     *
     * Returns the full url including scheme, domain, port, base path, and path
     * @param  bool   $withQueryString
     * @return string
     */
    public function currentUrl(bool $withQueryString = false): string
    {
        return $this->uri->getBaseUrl() . $this->currentPath($withQueryString);
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
     * @param array $options   Options array, includes "sizes", "style"
     * @return string
     */
    public function getMediaSrcSet(string $filename = null, string $altText = null, array $options = null): ?string
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
            // Include only image variants, not the original.
            if ($filename !== $file->getFilename()) {
                // Only include in source set if width is non-zero (possible error)
                $info = getimagesize($file->getPathname());
                if (is_int($info[0]) && $info[0] > 0) {
                    $sources[$info[0]] = "$imageDir{$file->getFilename()} {$info[0]}w";
                }
            }
        }
        ksort($sources);

        $sourceSet = implode(",\n", $sources);
        $sizes = $options['sizes'] ?? '';
        $style = (isset($options['style'])) ? 'style="' . $options['style'] .'"' : '';

        // Create HTML source set string only if there is more than one media file
        $sourceSetString = (iterator_count($files) > 1) ? "srcset=\"$sourceSet\"\nsizes=\"$sizes\"\n" : '';
        $srcAttr = $this->getMediaPath($filename, 'xlarge');

        return $this->cache['mediaSrcSet'][$filename] = "<img $sourceSetString src=\"$srcAttr\" alt=\"$altText\" $style>\n";
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

    /**
     * Truncate HTML
     *
     * Accepts an HTML string, and returns just the unformatted text, truncated to the number of words
     * @param string $html Input HTML string
     * @param int, $characters Number of characters to return
     * @return string
     */
    public function truncateHtml(string $html, int $characters = 300): string
    {
        return $this->container->toolbox->truncateHtmlText($html, $characters);
    }

    // ---------------- Front End Functions ----------------

    /**
     * Get Path for Page
     *
     * Front end method to generate URL for pages. See pathFor() for other routes
     * Alias for pathFor('showPage', ['slug1', 'slug2]).
     * @param string  $pageSlug        Page slug
     * @param array   $collectionSlug  Collection slug
     * @return string                  The desired route path without the domain, but does include the basePath
     */
    public function getPathForPage(string $pageSlug, ?string $collectionSlug = null): string
    {
        // Determine if the request is for a Collection or a Page
        $data = [];
        if ($collectionSlug) {
            $data['slug1'] = $collectionSlug;
            $data['slug2'] = $pageSlug;
        } else {
            $data['slug1'] = $pageSlug;
        }

        return $this->pathFor('showPage', $data);
    }

    /**
     * Get All Block Elements HTML
     *
     * Gets all Element's HTML within a Block, rendered with data
     * @param  array $block Array of Elements within a Block
     * @return string|null
     */
    public function getBlockElementsHtml(?array $block): ?string
    {
        if (empty($block)) {
            return null;
        }

        $blockHtml = '';
        foreach ($block as $element) {
            $blockHtml .= $this->getElementHtml($element) . PHP_EOL;
        }

        return $blockHtml;
    }

    /**
     * Get HTML Element
     *
     * Gets Element HTML fragments rendered with data
     * @param  PitonEntity  $element Element values
     * @return string
     */
    public function getElementHtml(?PitonEntity $element): ?string
    {
        // Ensure we have an element type
        if (empty($element->template)) {
            throw new Exception("PitonCMS: Missing page element template");
        }

        try {
            return $this->container->view->fetch("elements/{$element->template}.html", ['element' => $element]);
        } catch (LoaderError $e) {
            // If template name is malformed, just return null to fail gracefully
            $this->container->logger->error('PitonCMS: Invalid element template name provided in Piton\Library\Twig\Front getElementHtml(): ' . $element->template);
            return null;
        }
    }

    /**
     * Get Collection Page List
     *
     * Get collection pages by collection ID
     * For use in page element as collection landing page
     * @param  int        $collectionId Collection ID
     * @param  int|null   $limit
     * @return array|null
     */
    public function getCollectionPages(?int $collectionId, int $limit = null): ?array
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');

        // Get collection pages
        return $pageMapper->findPublishedCollectionPagesById($collectionId, $limit);
    }

    /**
     * Get Collection Page List With Pagination
     *
     * Get collection pages by collection ID
     * For use in page element as collection landing page
     * @param  int        $collectionId Collection ID
     * @param  int|null   $resultsPerPage Null value defaults to config: $config['pagination']['resultsPerPage']
     * @return array|null
     */
    public function getCollectionPagesWithPagination(?int $collectionId, int $resultsPerPage = null): ?array
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $pagination = $this->getPagination();

        if ($resultsPerPage) {
            $pagination->setConfig(['resultsPerPage' => $resultsPerPage]);
        }

        // Get collection pages
        $collectionPages = $pageMapper->findPublishedCollectionPagesById($collectionId, $pagination->getLimit(), $pagination->getOffset());

        // Setup pagination
        $pagination->setTotalResultsFound($pageMapper->foundRows() ?? 0);

        return $collectionPages;
    }

    /**
     * Get Published Ranked Collection Pages
     *
     * Finds sorted multi collection published content, in a ranked order, with a limit.
     * Rank Methods:
     * - 'recent'  : Published date descending
     * - 'popular' : View count descending
     * - 'random'  : Random selection
     *
     * @param  string  $rankMethod
     * @param  int     $limit, default 10
     * @return array|null
     */
    public function getPublishedRankedCollectionPages(
        string $rankMethod,
        ?int $limit = 10
    ): ?array {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');

        return $pageMapper->findPublishedRankedCollectionPages($rankMethod, $limit);
    }

    /**
     * Get Search Results With Pagination
     *
     * Executes site search and returns an array of search results for published pages
     * @param  int|null   $resultsPerPage
     * @return array|null
     */
    public function getSearchResultsWithPagination(int $resultsPerPage = null): array
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $pagination = $this->getPagination();
        $pagination->setPagePath($this->container->router->pathFor('submitSearch'));

        // Set results per page, or default to Pagination config
        if ($resultsPerPage) {
            $pagination->setConfig(['resultsPerPage' => $resultsPerPage]);
        }

        // Get sanitized query string parameters and execute search
        $terms = htmlspecialchars($this->container->request->getQueryParam('terms', ''));
        $results = $pageMapper->searchContent($terms, $pagination->getLimit(), $pagination->getOffset()) ?? [];

        // Complete pagination setup
        $pagination->setTotalResultsFound($pageMapper->foundRows() ?? 0);

        return $results;
    }

    /**
     * Get Gallery by ID
     *
     * @param int $galleryId
     * @return array|null
     */
    public function getGallery(int $galleryId = null): ?array
    {
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');

        return $mediaMapper->findMediaByCategoryId($galleryId);
    }

    /**
     * Get Navigator
     *
     * Get navigation by name
     * @param  string $navigator
     * @return array|null
     */
    public function getNavigator(string $navigator): ?array
    {
        // Return cached navigator if available
        if (isset($this->cache['navigator'][$navigator])) {
            return $this->cache['navigator'][$navigator];
        }

        // Get dependencies
        $navigationMapper = ($this->container->dataMapper)('NavigationMapper');

        // Get requested navigation set
        $navList = $navigationMapper->findNavigation($navigator);

        // Get current URL path to find and set currentPage in navigation hierarchy
        // Also check for home page '/' and reset to match page slug
        $url = $this->uri->getPath();
        $url = ($url === '/') ? 'home' : ltrim($url, '/');

        return $this->cache['navigator'][$navigator] = $navigationMapper->buildNavigation($navList, $url);
    }

    /**
     * Get Navigation Link
     *
     * @param PitonEntity $navLink
     * @return string|null
     */
    public function getNavigationLink(PitonEntity $navLink): ?string
    {
        if (isset($navLink->url)) {
            // A custom URL
            return $navLink->url;
        } elseif (isset($navLink->page_slug)) {
            // A page link of any kind
            return $this->getPathForPage($navLink->page_slug, $navLink->collection_slug);
        } else {
            // otherwise return nothing
            return null;
        }
    }

    // ---------------- Back End Functions ----------------

    /**
     * Generate Key
     *
     * Generates unique key of n-length.
     * @param int $length length of key, optional (default=4)
     * @return string
     */
    public function uniqueKey(int $length = 4): string
    {
        return substr(base_convert(rand(1000000000, PHP_INT_MAX), 10, 36), 0, $length);
    }

    /**
     * Get Alert Messages
     *
     * Get flash and application alert notices to display.
     * @param  array  $context Twig context, includes controller 'alert' array
     * @param  string $key     Alert keys: severity|heading|message
     * @return array|null
     */
    public function getAlert(array $context): ?array
    {
        $session = $this->container->sessionHandler;

        // If AdminBaseController render() is called then alert data is provided to Twig context for this request
        // But if AdminBaseController redirect() was called in last request the alert was saved to flash session data
        if (!empty($context['alert'])) {
            $alert = $context['alert'];
        } else {
            $alert = $session->getFlashData('alert');
        }

        // Load any system messages (created outside of a session) from site settings (which is loaded from data_store in middleware)
        if (isset($this->container->settings['environment']['appAlert'])) {
            $appData = json_decode($this->container->settings['environment']['appAlert'], true);
            if (is_array($appData)) {
                // Append to $alert array, if exists
                $alert = array_merge($alert ?? [], $appData);

                // Unset app alert data
                $dataMapper = ($this->container->dataMapper)('DataStoreMapper');
                $dataMapper->unsetAppAlert();
            }
        }

        return $alert;
    }

    /**
     * Get All Collections
     *
     * Get list of collections
     * @param  void
     * @return array|null
     */
    public function getCollections(): ?array
    {
        if (isset($this->cache['collections'])) {
            return $this->cache['collections'];
        }

        $collectionMapper = ($this->container->dataMapper)('CollectionMapper');

        // Return and cache
        return $this->cache['collections'] = $collectionMapper->find();
    }

    /**
     * Get Page Templates
     *
     * Get list of page templates
     * @param  void
     * @return array|null
     */
    public function getPageTemplates(): ?array
    {
        if (isset($this->cache['pageTemplates'])) {
            return $this->cache['pageTemplates'];
        }

        $definition = $this->container->jsonDefinitionHandler;

        // Return and cache
        return $this->cache['pageTemplates'] = $definition->getPages();
    }

    /**
     * Get Media Categories
     *
     * Get all media categories
     * @param  void
     * @return array|null
     */
    public function getMediaCategories(): ?array
    {
        if (isset($this->cache['mediaCategories'])) {
            return $this->cache['mediaCategories'];
        }

        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');

        // Get all media categories and create key: value pair array
        $categories = $mediaCategoryMapper->findCategories() ?? [];
        $categories = array_column($categories, 'category', 'id');

        return $this->cache['mediaCategories'] = $categories;
    }

    /**
     * Get Elements
     *
     * Optionally filter list of elements
     * @param  array|null $filter Return only listed elements
     * @return array|null
     */
    public function getElements(array $filter = null): ?array
    {
        // Set cached elements, if not set
        if (!isset($this->cache['elements'])) {
            // Get dependencies
            $definition = $this->container->jsonDefinitionHandler;
            $elements = $definition->getElements();
            $elements = array_combine(array_column($elements, 'filename'), $elements);

            $this->cache['elements'] = $elements;
        }

        if (!$filter) {
            return $this->cache['elements'];
        }

        $filter = array_flip($filter);

        return array_intersect_key($this->cache['elements'], $filter);
    }

    /**
     * Get Unread Message Count
     *
     * Gets count of unread messages
     * @param  void
     * @return int|null
     */
    public function getUnreadMessageCount(): ?int
    {
        if (isset($this->cache['unreadMessageCount'])) {
            return $this->cache['unreadMessageCount'];
        }

        $messageMapper = ($this->container->dataMapper)('MessageMapper');
        $count = $messageMapper->findUnreadCount();

        return $this->cache['unreadMessageCount'] = ($count === 0) ? null : $count;
    }

    /**
     * Get Session Data
     *
     * Gets data from session handler
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function getSessionData(string $key = null, string $default = null)
    {
        return $this->container->sessionHandler->getData($key, $default);
    }

    /**
     * Get JS File Source
     *
     * Returns <script> tag with link to JS source
     * Uses compiled JS in /dist, unless requested to be type=module for development
     * @param string $file JS file to load without the extension
     * @param bool   $module Flag to return type=module
     */
    public function getJsFileSource(string $file, bool $module = false)
    {
        if ($file === 'ckeditor') {
            // First check if the request is for the ckeditor file, which does not depend on modules
            $source = $this->baseUrl() . "/admin/ckeditor5/build/$file.js?v=" . $this->container['settings']['environment']['assetVersion'];
        } elseif ($this->container['settings']['environment']['production'] || !$module) {
            // Next, for other JS files, check the production and not a module flag to return the /dist version
            $source = $this->baseUrl() . "/admin/js/dist/$file.js?v=" . $this->container['settings']['environment']['assetVersion'];
        } else {
            // Finally return the module JS since this is a non-production or development environment
            $source = $this->baseUrl() . "/admin/js/$file.js?v=" . $this->container['settings']['environment']['assetVersion'];
        }

        // Set module attribute if requested
        $moduleType = ($module) ? 'type="module"' : '';

        // Set nonce
        $nonce = $this->container['settings']['environment']['cspNonce'];

        return "<script nonce=\"$nonce\" src=\"$source\" $moduleType></script>";
    }

    /**
     * Current Route Parent
     *
     * If the supplied route name resolves as the parent in the navigation hierarcy, returns the returnValue string
     * @param  string $routeName   Name of the route to test
     * @param  string $returnValue Value to return
     * @return string|null
     */
    public function currentRouteParent(string $routeName, string $returnValue = 'active'): ?string
    {
        // Trace current page route name through AdminSiteHierarchy array to find parent with null value
        $route = $this->container->settings['environment']['currentRouteName'];

        while (self::AdminSiteHierarchy[$route] ?? false) {
            // Check for recursion in this while loop if the array is accidentally setup incorrectly
            if ($route === self::AdminSiteHierarchy[$route]) {
                throw new Exception("PitonCMS: Recursive reference in Twig Admin AdminSiteHierarchy");
            }

            $route = self::AdminSiteHierarchy[$route];
        }

        if ($route === $routeName) {
            return $returnValue;
        }

        return null;
    }

    /**
     * Get Max Upload Size
     *
     * Returns the minimum of ini settings: post_max_size, upload_max_filesize, memory_limit
     * @param void
     * @return int|null
     */
    public function getMaxUploadSize(): ?int
    {
        function parseSize($val)
        {
            switch (substr($val, -1)) {
                case 'M':
                case 'm':
                    return (int)$val * 1048576;
                case 'K':
                case 'k':
                    return (int)$val * 1024;
                case 'G':
                case 'g':
                    return (int)$val * 1073741824;
                default:
                    return $val;
            }
        }

        $postSize = parseSize(ini_get('post_max_size'));
        $fileSize = parseSize(ini_get('upload_max_filesize'));
        $memSize = parseSize(ini_get('memory_limit'));

        return min($postSize, $fileSize, $memSize);
    }

    /**
     * Get JS Custom Extensions
     *
     * Returns HTML script element to load custom JS extensions
     * @param  string|null $scope 'site' for site wide extension, null for current path specific extension
     * @return string|null
     */
    public function getJsFileExtensions(?string $scope = null): ?string
    {
        // Get nonce and asset version
        $nonce = $this->container['settings']['environment']['cspNonce'];
        $assetVersion = '?v=' . $this->container['settings']['environment']['assetVersion'];

        // Check for site wide extension
        if ($scope === 'site') {
            // Check if file exists
            if (file_exists(ROOT_DIR . 'public/extensions/extension.js')) {
                $siteExtensionSource = '/extensions/extension.js' . $assetVersion;

                return "<script nonce=\"$nonce\" src=\"$siteExtensionSource\"></script>";
            }

            return null;
        }

        // Check for current path specific extension
        $currentPath = $this->uri->getPath();
        $currentPath = preg_replace('/\d+$/i', '', $currentPath);

        $extensionSource = '/extensions' . $currentPath . 'extension.js';

        // Check if file exists
        if (file_exists(ROOT_DIR . 'public' . $extensionSource)) {
            $extensionSource .= $assetVersion;
            return "<script nonce=\"$nonce\" src=\"$extensionSource\"></script>";
        }

        return null;
    }
}
