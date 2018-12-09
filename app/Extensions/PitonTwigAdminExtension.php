<?php
/**
 * Custom Piton Admin Extensions for Twig
 *
 * Has Twig functions and filters to be used on the admin site.
 * Note: extended methods such as getFilters and getFunctions must merge parent functions.
 */
namespace Piton\Extensions;

use Interop\Container\ContainerInterface;

class PitonTwigAdminExtension extends PitonTwigBaseExtension
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
            new \Twig_SimpleFunction('getThemes', array($this, 'getThemes')),
            new \Twig_SimpleFunction('getThemeLayouts', array($this, 'getThemeLayouts')),
            new \Twig_SimpleFunction('uniqueArrayKey', array($this, 'uniqueArrayKey')),
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
        $themes = ['default'];
        foreach (new \DirectoryIterator(ROOT_DIR . 'themes/') as $dirObject) {
            // Ignore dot files, and skip default theme as we will force that option to the top
            if (!$dirObject->isDir() || $dirObject->isDot() || $dirObject->getFilename() === 'default') {
                continue;
            }

            $themes[] = $dirObject->getFilename();
        }

        return $themes;
    }

    /**
     * Get Layout File Names
     *
     * For the current theme, gets layout file names, and strips the '.html'
     * @param none
     * @return mixed array of layout file names, or null
     */
    public function getThemeLayouts()
    {
        // Get layout templates
        $layouts = [];
        foreach (new \DirectoryIterator(ROOT_DIR . 'themes/' . $this->siteSettings['theme'] . '/templates/layouts/') as $dirObject) {
            if ($dirObject->isDir() ||
                $dirObject->isDot() ||
                substr($dirObject->getFilename(), 0, 1) === '.' ||
                substr($dirObject->getFilename(), 0, 1) === '_' ||
                substr($dirObject->getFilename(), -5) === '.json'
            ) {
                continue;
            }

            $layouts[] = pathinfo($dirObject->getFilename(), PATHINFO_FILENAME);
        }

        return $layouts;
    }

    /**
     * Tests if Numeric
     *
     * @param mixed
     * @return boolean
     */
    public function uniqueArrayKey()
    {
        return substr(base_convert(rand(1000000000, PHP_INT_MAX), 10, 36), 0, 4);
    }
}
