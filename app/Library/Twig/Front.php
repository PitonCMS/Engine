<?php
/**
 * Piton Front End Twig Extension
 *
 * Custom Twig functions and filters to be used on the public facing site.
 * Note: extended methods such as getFilters and getFunctions must merge parent functions.
 */
namespace Piton\Library\Twig;

use Interop\Container\ContainerInterface;

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
        // Element array example of keys
        // [element_type] => hero
        // [title] => Hero Image
        // [content] => <h2>Call to action!</h2>
        // [excerpt] => Call to action! Call to action!
        // [collection_id] => 1
        // [media_id] => 1
        // [media_path] => https://unsplash.it/600

        // Ensure we have an element type
        if (!isset($element['element_type']) && empty($element['element_type'])) {
            throw new Exception("Missing element_type");
        }

        // Assign data
        $data['title'] = $element['title'];
        $data['content'] = $element['content'];
        $data['mediaPath'] = $element['media_path'];

        // Return template
        $includeElement = $element['element_type'] . '.html';

        return $this->container->view->fetch("elements/$includeElement", ['data' => $data]);
    }
}
