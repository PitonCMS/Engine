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
 * Piton Front End Twig Extension
 *
 * Custom Twig functions and filters to be used on the public facing site.
 * Note: extended methods such as getFilters and getFunctions must merge parent functions.
 */
class Front extends Base
{

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

        return $this->container->view->fetch("elements/{$element->template}", ['data' => $element]);
    }

    /**
     * Get Collection Page List
     *
     * Get collection pages by collection ID
     * For use in page element as collection landing page
     * @param  int   $collectionId Collection ID
     * @return mixed               Array | null
     */
    public function getCollectionPages($collectionId)
    {
        $pageMapper = ($this->container->dataMapper)('PageMapper');

        // Page Collection
        $data = $pageMapper->findCollectionPagesById($collectionId);

        return $data;
    }
}
