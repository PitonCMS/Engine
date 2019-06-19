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
use Twig\Error\LoaderError;

/**
 * Piton Front End Twig Extension
 *
 * Custom Twig functions and filters to be used on the public facing site.
 * Note: extended methods such as getFilters and getFunctions must merge parent functions.
 */
class Front extends Base
{
    /**
     * Cache Navigations
     * @var array
     */
    protected $navigation;

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
     * Register Custom Filters
     */
    public function getFilters()
    {
        return array_merge(parent::getFilters(), [
            // Custom filters
        ]);
    }

    /**
     * Register Custom Functions
     */
    public function getFunctions()
    {
        return array_merge(parent::getFunctions(), [
            new \Twig_SimpleFunction('getBlockElementsHtml', [$this, 'getBlockElementsHtml'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('getElementHtml', [$this, 'getElementHtml'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('getCollectionPages', [$this, 'getCollectionPages']),
            new \Twig_SimpleFunction('getGallery', [$this, 'getGallery']),
            new \Twig_SimpleFunction('getNavigator', [$this, 'getNavigator']),
        ]);
    }

    /**
     * Get All Block Elements HTML
     *
     * Gets all all Element's HTML within a Block, rendered with data
     * @param  array $block Array of Elements within a Block
     * @return string        HTML
     */
    public function getBlockElementsHtml($block)
    {
        if (empty($block)) {
            return '';
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
     * @param  array  $element Element values
     * @return string          HTML
     */
    public function getElementHtml($element)
    {
        // Ensure we have an element type
        if (!isset($element->template) && empty($element->template)) {
            throw new Exception("Missing page element template");
        }

        try {
            $html = $this->container->view->fetch("elements/{$element->template}", ['element' => $element]);
        } catch (LoaderError $e) {
            // If template name is malformed, just return empty string to fail gracefully
            $this->container->logger->error('PitonCMS: Invalid element template name provided in Piton\Library\Twig\Front getElementHtml(): ' . $element->template);
            $html = '';
        }

        return $html;
    }

    /**
     * Get Collection Page List
     *
     * Get collection pages by collection slug
     * For use in page element as collection landing page
     * @param  int   $collectionSlug Collection Slug
     * @return mixed               Array | null
     */
    public function getCollectionPages($collectionSlug)
    {
        $pageMapper = ($this->container->dataMapper)('PageMapper');

        return $pageMapper->findCollectionPagesBySlug($collectionSlug);
    }

    /**
     * Get Gallery by ID
     *
     * @param int $galleryId
     * @return mixed
     */
    public function getGallery(int $galleryId = null)
    {
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');

        return $mediaMapper->findMediaByCategoryId($galleryId);
    }

    /**
     * Get Navigator
     *
     * Get navigation by name
     * @param  string $navigator
     * @return mixed
     */
    public function getNavigator(string $navigator)
    {
        // Return cached navigator if available
        if (isset($this->navigation[$navigator])) {
            return $this->navigation[$navigator];
        }

        // Get dependencies
        $navigationMapper = ($this->container->dataMapper)('NavigationMapper');

        // Get current URL path to find currentPage in navigation
        // And check if home page '/' and reset to match page slug
        $url = $this->uri->getPath();
        $url = ($url === '/') ? 'home' : ltrim($url, '/');

        return $this->navigator[$navigator] = $navigationMapper->findNavHierarchy($navigator, $url);
    }
}
