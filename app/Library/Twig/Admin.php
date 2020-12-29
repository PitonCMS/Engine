<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Library\Twig;

use Twig\TwigFunction;
use Exception;

/**
 * Piton Back End Admin Twig Extension
 *
 * Has Twig functions and filters to be used on the admin site.
 * Note: extended methods such as getFilters and getFunctions must merge parent functions.
 */
class Admin extends Base
{
    /**
     * Admin Site Hierarchy
     * pageRouteName => parentPageRouteName
     * Null values represent top level navigation routes
     * @var array
     */
    protected const SiteHierarchy = [
        // Level 0 pages
        'adminHome' => null,
        'adminPage' => null,
        'adminMedia' => null,
        'adminNavigation' => null,
        'adminMessage' => null,
        'adminSetting' => null,
        'adminHelp' =>  null,
        // Level 1 pages
        'adminPageEdit' => 'adminPage',
        'adminNavigationEdit' => 'adminNavigation',
        'adminSettingEdit' => 'adminSetting',
        'adminSitemap' => 'adminSetting',
        'adminCollection' => 'adminSetting',
        'adminMediaCategoryEdit' => 'adminSetting',
        'adminUser' => 'adminSetting',
        // Level 2 pages
        'adminUserEdit' => 'adminUser',
        'adminCollectionEdit' => 'adminCollection',
    ];

    /**
     * Register Global variables
     *
     * @param void
     * @return array
     */
    public function getGlobals(): array
    {
        return array_merge_recursive(parent::getGlobals(), [
            'site' => [
                'environment' => [
                    'projectDir' => basename(ROOT_DIR),
                    'sessionUserId' => $this->container->sessionHandler->getData('user_id'),
                    'sessionUserFirstName' => $this->container->sessionHandler->getData('first_name'),
                    'sessionUserLastName' => $this->container->sessionHandler->getData('last_name'),
                    ]
            ]
        ]);
    }

    /**
     * Register Custom Filters
     *
     * @param void
     * @return array
     */
    public function getFilters(): array
    {
        return array_merge_recursive(parent::getFilters(), [
            // Custom filters
        ]);
    }

    /**
     * Register Custom Functions
     *
     * @param void
     * @return array
     */
    public function getFunctions(): array
    {
        return array_merge(parent::getFunctions(), [
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
        ]);
    }

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

        $moduleType = ($module) ? 'type="module"' : '';

        return "<script src=\"$source\" $moduleType></script>";
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
        // Trace current page route name through SiteHierarchy array to find parent with null value
        $route = $this->container->settings['environment']['currentRouteName'];

        while (self::SiteHierarchy[$route] ?? false) {
            // Check for recursion in this while loop if the array is accidentally setup incorrectly
            if ($route === self::SiteHierarchy[$route]) {
                throw new Exception("PitonCMS: Recursive reference in Twig Admin SiteHierarchy");
            }

            $route = self::SiteHierarchy[$route];
        }

        if ($route === $routeName) {
            return $returnValue;
        }

        return null;
    }
}
