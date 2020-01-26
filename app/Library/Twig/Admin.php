<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Twig;

use Psr\Container\ContainerInterface;
use Twig\TwigFunction;

/**
 * Piton Back End Admin Twig Extension
 *
 * Has Twig functions and filters to be used on the admin site.
 * Note: extended methods such as getFilters and getFunctions must merge parent functions.
 */
class Admin extends Base
{

    /**
     * Elements Cache
     * For multiple calls on getCustomElements()
     * @var array
     */
    protected $elements;

    /**
     * Collections Cache
     * For multiple calls on getCollections()
     * @var array
     */
    protected $collections;

    /**
     * Gallery Cache
     * For multiple calls on getGalleries()
     * @var array
     */
    protected $galleries;

    /**
     * Pages Cache
     * For multiple calls on getNavPages()
     * @var array
     */
    protected $pages;

    /**
     * Constructor
     *
     * @param object Psr\Container\ContainerInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * Register Global variables
     */
    public function getGlobals(): array
    {
        return array_merge_recursive(parent::getGlobals(), [
            'site' => [
                'settings' => ['projectDir' => basename(ROOT_DIR)]
            ]
        ]);
    }

    /**
     * Register Custom Filters
     */
    public function getFilters(): array
    {
        return array_merge_recursive(parent::getFilters(), [
            // Custom filters
        ]);
    }

    /**
     * Register Custom Functions
     */
    public function getFunctions(): array
    {
        return array_merge(parent::getFunctions(), [
            new TwigFunction('uniqueKey', [$this, 'uniqueKey']),
            new TwigFunction('getAlert', [$this, 'getAlert'], ['needs_context' => true]),
            new TwigFunction('getCollections', [$this, 'getCollections']),
            new TwigFunction('getGalleries', [$this, 'getGalleries']),
            new TwigFunction('getElements', [$this, 'getElements']),
            new TwigFunction('getUnreadMessageCount', [$this, 'getUnreadMessageCount']),
            new TwigFunction('getNavPages', [$this, 'getNavPages']),
        ]);
    }

    /**
     * Generate Key
     *
     * Generates unique key of n-length.
     * @param int  $length length of key, optional (default=4)
     * @return str
     */
    public function uniqueKey($length = 4)
    {
        return substr(base_convert(rand(1000000000, PHP_INT_MAX), 10, 36), 0, $length);
    }

    /**
     * Get Alert Messages
     *
     * Get alert data. Returns null if no alert found.
     * @param  array  $context Twig context, includes controller alert array
     * @param  string $key     Alert keys: severity|heading|message
     * @return mixed           array|string|null
     */
    public function getAlert($context, $key = null)
    {
        $session = $this->container->sessionHandler;

        // Get alert notices from page context, or failing that then session flash data
        $alert = (isset($context['alert'])) ? $context['alert'] : $session->getFlashData('alert');

        if ($key === null) {
            return $alert;
        }

        if (isset($alert[$key])) {
            if ($key === 'message') {
                return '<ul><li>' . implode('</li><li>', $alert['message']) . '</ul>';
            }

            return $alert[$key];
        }

        return null;
    }

    /**
     * Get Collections
     *
     * Get list of distinct collections
     * @param  void
     * @return mixed Array | null
     */
    public function getCollections()
    {
        if ($this->collections) {
            return $this->collections;
        }

        $pageMapper = ($this->container->dataMapper)('PageMapper');

        // Structure return array
        $data = $pageMapper->findCollections();
        $collections = array_map(function ($col) {
            return [
                'name' => ucfirst($col->collection_slug),
                'value' => $col->collection_slug
            ];
        }, $data);

        return $this->collections = $collections;
    }

    /**
     * Get Gallery Options
     *
     * Get all gallery media categories
     * @param  void
     * @return mixed Array | null
     */
    public function getGalleries()
    {
        if ($this->galleries) {
            return $this->galleries;
        }

        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');

        return $this->galleries = $mediaCategoryMapper->findCategories();
    }

    /**
     * Get Elements
     *
     * @param  void
     * @return array
     */
    public function getElements()
    {
        // Return cached set of elements, if available
        if (isset($this->elements)) {
            return $this->elements;
        }

        // Get dependencies
        $definition = $this->container->definition;
        return $this->elements = $definition->getElements();
    }

    /**
     * Get Unread Message Count
     *
     * Gets count of unread messages
     * @param  void
     * @return mixed
     */
    public function getUnreadMessageCount()
    {
        $messageMapper = ($this->container->dataMapper)('MessageMapper');
        $count = $messageMapper->findUnreadCount();

        return ($count === 0) ? null : $count;
    }

    /**
     * Get Pages
     *
     * Gets a list of all pages, including unpublished
     * @param void
     * @return mixed
     */
    public function getNavPages()
    {
        // Get cached pages if available
        if ($this->pages) {
            return $this->pages;
        }

        // Otherwise fetch all pages
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        return $this->pages = $pageMapper->findPages(true);
    }
}
