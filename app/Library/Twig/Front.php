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

use Piton\Models\Entities\PitonEntity;
use Twig\Error\LoaderError;
use Twig\TwigFunction;
use Exception;

/**
 * Piton Front End Twig Extension
 *
 * Custom Twig functions and filters to be used on the public facing site.
 * Note: extended methods such as getFilters and getFunctions must merge parent functions.
 */
class Front extends Base
{
    /**
     * Register Custom Filters
     *
     * @param void
     * @return array
     */
    public function getFilters(): array
    {
        return array_merge(parent::getFilters(), [
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
            new TwigFunction('getBlockElementsHtml', [$this, 'getBlockElementsHtml'], ['is_safe' => ['html']]),
            new TwigFunction('getElementHtml', [$this, 'getElementHtml'], ['is_safe' => ['html']]),
            new TwigFunction('getCollectionPages', [$this, 'getCollectionPages']),
            new TwigFunction('getGallery', [$this, 'getGallery']),
            new TwigFunction('getNavigator', [$this, 'getNavigator']),
        ]);
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
        if (!isset($element->template) && empty($element->template)) {
            throw new Exception("PitonCMS: Missing page element template");
        }

        try {
            return $this->container->view->fetch("elements/{$element->template}", ['element' => $element]);
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
     * @param  int   $collectionId Collection ID
     * @return array|null
     */
    public function getCollectionPages(int $collectionId): ?array
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');

        // Get collection pages
        return $pageMapper->findCollectionPagesById($collectionId);
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

        // Get current URL path to find currentPage in navigation
        // And check if home page '/' and reset to match page slug
        $url = $this->uri->getPath();
        $url = ($url === '/') ? 'home' : ltrim($url, '/');

        return $this->cache['navigator'][$navigator] = $navigationMapper->findNavHierarchy($navigator, $url);
    }
}
