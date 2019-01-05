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
            new \Twig_SimpleFunction('assetsPath', [$this, 'assetsPath']),
            new \Twig_SimpleFunction('getElementHtml', [$this, 'getElementHtml'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('getCollection', [$this, 'getCollection']),
        ]);
    }

    /**
     * Get Base Path for Theme Assets
     *
     * @param none
     * @return array
     */
    public function assetsPath()
    {
        return $this->basePath() . '/assets/' . $this->siteSettings['theme'];
    }

    /**
     * Get HTML Element
     *
     * @param array $element Element values
     * @return string HTML
     */
    public function getElementHtml($element)
    {
        // Ensure we have an element type
        if (!isset($element->element_type) && empty($element->element_type)) {
            throw new Exception("Missing element_type");
        }

        // Template to render
        $includeElement = $element->element_type . '.html';

        return $this->container->view->fetch("elements/$includeElement", ['data' => $element]);
    }

    /**
     * Get Collection
     *
     * Get collection summary and details by collection ID
     * For use in page element as collection landing page
     * @param  int   $collectionId Collection ID
     * @return mixed               Array | null
     */
    public function getCollection($collectionId)
    {
        $dataMapper = $this->container->dataMapper;
        $CollectionMapper = $dataMapper('CollectionMapper');
        $CollectionDetailMapper = $dataMapper('CollectionDetailMapper');

        // Collection
        $data = $CollectionMapper->findById($collectionId);

        // Published Details
        if (!empty($data)) {
            $data->details = $CollectionDetailMapper->findByCollectionId($collectionId);
        }

        return $data;
    }
}
