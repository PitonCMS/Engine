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
     * Elements Cache
     * For multiple calls on getCustomElements()
     * @var array
     */
    protected $elementOptions;

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
     * Constructor
     *
     * @param obj Interop\Container\ContainerInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * Register Global variables
     */
    public function getGlobals()
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
    public function getFilters()
    {
        return array_merge_recursive(parent::getFilters(), [
            // Custom filters
        ]);
    }

    /**
     * Register Custom Functions
     */
    public function getFunctions()
    {
        return array_merge(parent::getFunctions(), [
            new \Twig_SimpleFunction('getThemes', [$this, 'getThemes']),
            new \Twig_SimpleFunction('uniqueKey', [$this, 'uniqueKey']),
            new \Twig_SimpleFunction('getAlert', [$this, 'getAlert'], ['needs_context' => true]),
            new \Twig_SimpleFunction('getCollections', [$this, 'getCollections']),
            new \Twig_SimpleFunction('getGalleries', [$this, 'getGalleries']),
            new \Twig_SimpleFunction('getElements', [$this, 'getElements']),
            new \Twig_SimpleFunction('getUnreadMessageCount', [$this, 'getUnreadMessageCount']),
        ]);
    }

    /**
     * Get Array of Themes
     *
     * @param none
     * @return array
     */
    public function getThemes()
    {
        $json = $this->container->json;

        if (null === $definition = $json->getJson(ROOT_DIR . 'structure/definitions/themes.json', 'themes')) {
            throw new Exception('PitonCMS: Get themes exception: ' . implode($json->getErrorMessages(), ','));
        }

        return array_column($definition->themes, 'name', 'value');
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
        // Get dependencies
        $toolbox = $this->container->toolbox;
        $json = $this->container->json;

        if (isset($this->elementOptions)) {
            return $this->elementOptions;
        }

        // Get all JSON files in directory
        $jsonPath = ROOT_DIR . "structure/definitions/elements/";
        $elements = [];

        foreach ($toolbox->getDirectoryFiles($jsonPath) as $key => $file) {
            if (null === $definition = $json->getJson($jsonPath . $file['filename'], 'element')) {
                throw new Exception('Element JSON definition error: ' . print_r($json->getErrorMessages(), true));
            } else {
                $definition->filename = $file['filename'];
                $elements[] = $definition;
            }
        }

        return $this->elementOptions = $elements;
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
}
