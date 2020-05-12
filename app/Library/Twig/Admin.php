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
            new TwigFunction('getPageTemplates', [$this, 'getPageTemplates']),
            new TwigFunction('getMediaCategories', [$this, 'getMediaCategories']),
            new TwigFunction('getElements', [$this, 'getElements']),
            new TwigFunction('getUnreadMessageCount', [$this, 'getUnreadMessageCount']),
            new TwigFunction('getSessionData', [$this, 'getSessionData']),
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
        // or if AdminBaseController redirect() was called in last request then saved to flash session data
        $alert = $context['alert'] ?? $session->getFlashData('alert');

        // Load any system messages (created outside of a session) from site settings (which is loaded from data_store in middleware)
        if (isset($this->container->settings['site']['appAlert'])) {
            $appData = json_decode($this->container->settings['site']['appAlert'], true);
            if (is_array($appData)) {
                // Append to $alert array
                $alert = array_merge($alert, $appData);

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
     * Get all media category galleries
     * @param  void
     * @return array|null
     */
    public function getMediaCategories(): ?array
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
}
