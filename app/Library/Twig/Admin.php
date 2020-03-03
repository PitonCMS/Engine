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

/**
 * Piton Back End Admin Twig Extension
 *
 * Has Twig functions and filters to be used on the admin site.
 * Note: extended methods such as getFilters and getFunctions must merge parent functions.
 */
class Admin extends Base
{
    /**
     * Cache
     * @var array
     */
    protected $cache = [];

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
                'settings' => ['projectDir' => basename(ROOT_DIR)]
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
     * Get alert data. Returns null if no alert found.
     * @param  array  $context Twig context, includes controller alert array
     * @param  string $key     Alert keys: severity|heading|message
     * @return array|string|null
     */
    public function getAlert(array $context, string $key = null)
    {
        $session = $this->container->sessionHandler;

        // Get alert notices from page context, or failing that then session flash data
        $alert = $context['alert'] ?? $session->getFlashData('alert');

        if ($key === null) {
            return $alert;
        }

        if (isset($alert[$key])) {
            if ($key === 'message') {
                return ($alert['message']) ? '<ul>' . implode('</li><li>', $alert['message']) . '</ul>' : null;
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
     * @return array|null
     */
    public function getCollections(): ?array
    {
        if (isset($this->cache['collections'])) {
            return $this->cache['collections'];
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

        return $this->cache['collections'] = $collections;
    }

    /**
     * Get Gallery Options
     *
     * Get all gallery media categories
     * @param  void
     * @return array|null
     */
    public function getGalleries(): ?array
    {
        if (isset($this->cache['galleries'])) {
            return $this->cache['galleries'];
        }

        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');

        return $this->cache['galleries'] = $mediaCategoryMapper->findCategories();
    }

    /**
     * Get Elements
     *
     * @param  void
     * @return array
     */
    public function getElements(): array
    {
        // Return cached set of elements, if available
        if (isset($this->cache['elements'])) {
            return $this->cache['elements'];
        }

        // Get dependencies
        $definition = $this->container->jsonDefinitionHandler;
        return $this->cache['elements'] = $definition->getElements();
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
        $messageMapper = ($this->container->dataMapper)('MessageMapper');
        $count = $messageMapper->findUnreadCount();

        return ($count === 0) ? null : $count;
    }

    /**
     * Get Pages
     *
     * Gets a list of all pages, including unpublished
     * @param void
     * @return array
     */
    public function getNavPages(): ?array
    {
        // Get cached pages if available
        if (isset($this->cache['pages'])) {
            return $this->cache['pages'];
        }

        // Otherwise fetch all pages
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        return $this->cache['pages'] = $pageMapper->findPages(true);
    }
}
