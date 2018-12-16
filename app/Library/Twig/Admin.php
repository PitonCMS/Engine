<?php
/**
 * Piton Back End Admin Twig Extension
 *
 * Has Twig functions and filters to be used on the admin site.
 * Note: extended methods such as getFilters and getFunctions must merge parent functions.
 */
namespace Piton\Library\Twig;

use Interop\Container\ContainerInterface;

class Admin extends Base
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
            new \Twig_SimpleFunction('getThemes', [$this, 'getThemes']),
            new \Twig_SimpleFunction('getThemeLayouts', [$this, 'getThemeLayouts']),
            new \Twig_SimpleFunction('uniqueArrayKey', [$this, 'uniqueArrayKey']),
            new \Twig_SimpleFunction('getAlert', [$this, 'getAlert'], ['needs_context' => true]),
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
        $pathToLayouts = ROOT_DIR . 'themes/' . $this->siteSettings['theme'] . '/templates/layouts/';
        foreach (new \DirectoryIterator($pathToLayouts) as $dirObject) {
            if ($dirObject->isDir() ||
                $dirObject->isDot() ||
                substr($dirObject->getFilename(), 0, 1) === '.' ||
                substr($dirObject->getFilename(), 0, 1) === '_' ||
                pathinfo($dirObject->getFilename(), PATHINFO_EXTENSION) === 'json'
            ) {
                continue;
            }

            // Split camelCase filenames and upper case first letters into title case,
            // and assign to array using [fileName] = Readable File Name
            $fileName = pathinfo($dirObject->getFilename(), PATHINFO_FILENAME);
            $ReadFileName = preg_replace("/([a-z].[^A-Z]+)/s", "$1 ", $fileName);
            $ReadFileName = ucwords($ReadFileName);
            $layouts[$fileName] = $ReadFileName;
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

    /**
     * Get Alert Messages
     *
     * Get alert data. Returns null if no alert found.
     * @param array $context Page data
     * @param string $key Alert keys: severity|heading|message
     * @returns mixed array|string|null
     */
    public function getAlert($context, $key = null)
    {
        $session = $this->container->sessionHandler;

        // Get alert notices from page context, or failing that then session flash data
        $alert = null;
        $alert = (isset($context['alert'])) ? $context['alert'] : $session->getFlashData('alert');

        if ($key === null) {
            return $alert;
        }

        if ($key === 'message' && isset($alert['message'])) {
            return '<ul><li>' . implode('</li><li>', $alert['message']) . '</li></ul>';
        }

        if (isset($alert[$key])) {
            return $alert[$key];
        }

        return null;
    }
}
