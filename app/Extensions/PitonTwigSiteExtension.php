<?php
/**
 * Custom Piton Site Extensions for Twig
 *
 * Has Twig functions and filters to be used on the public facing site.
 * Note: extended methods such as getFilters and getFunctions must merge parent functions.
 */
namespace Piton\Extensions;

use Interop\Container\ContainerInterface;

class PitonTwigSiteExtension extends PitonTwigBaseExtension
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
            new \Twig_SimpleFunction('assetsPath', array($this, 'assetsPath')),
            new \Twig_SimpleFunction('fetchElementHtml', array($this, 'fetchElementHtml'), array('is_safe' => array('html'))),
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
     * Fetch Element
     *
     * @param string elementType Matches include of the same name
     * @param string title
     * @param string content
     * @return string HTML
     */
    public function fetchElementHtml($elementType, $title = null, $content = null, $collectionId = null, $mediaId = null, $mediaPath = null)
    {
        // Assign data
        $data['title'] = $title;
        $data['content'] = $content;
        $data['mediaPath'] = $mediaPath;

        // Return template
        $includeElement = $elementType . '.html';
        // TODO Put in some sort of error handling if include element does not exist

        return $this->container->view->fetch("elements/$includeElement", ['data' => $data]);
    }
}
