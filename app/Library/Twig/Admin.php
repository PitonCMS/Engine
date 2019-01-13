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
 * Piton Back End Admin Twig Extension
 *
 * Has Twig functions and filters to be used on the admin site.
 * Note: extended methods such as getFilters and getFunctions must merge parent functions.
 */
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
            new \Twig_SimpleFunction('getThemePageTemplates', [$this, 'getThemePageTemplates']),
            new \Twig_SimpleFunction('uniqueKey', [$this, 'uniqueKey']),
            new \Twig_SimpleFunction('getAlert', [$this, 'getAlert'], ['needs_context' => true]),
            new \Twig_SimpleFunction('getSettingOptions', [$this, 'getSettingOptions']),
            new \Twig_SimpleFunction('getCollections', [$this, 'getCollections']),
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
        $toolbox = $this->container->toolbox;
        $themes = $toolbox->getDirectoryFiles(ROOT_DIR . 'themes', 'default');
        $themeOptions['default'] = 'Default';

        foreach ($themes as $row) {
            $themeOptions[$row['filename']] = $row['readname'];
        }

        return $themeOptions;
    }

    /**
     * Get Theme Page Templates
     *
     * For the current theme
     * @param  void
     * @return array
     */
    public function getThemePageTemplates()
    {
        $toolbox = $this->container->toolbox;
        $path = ROOT_DIR . 'themes/' . $this->siteSettings['theme'] . '/templates/layouts/';
        $layoutFiles = $toolbox->getDirectoryFiles($path, ['^_.+','\.html']);
        $layouts = [];

        foreach ($layoutFiles as $row) {
            $layouts[$row['basename']] = $row['readname'];
        }

        return $layouts;
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

    /**
     * Get Setting Select Options
     *
     * Accepts a setting_key string and returns an array of selection options
     * Limited to: "theme", "dateFormat", "urlScheme"
     * @param string $settingKey Setting key string
     * @return array
     */
    public function getSettingOptions($settingKey)
    {
        if ($settingKey === 'theme') {
            return $this->getThemes();
        }

        if ($settingKey === 'dateFormat') {
            return [
                'mm/dd/yyyy' => 'mm/dd/yyyy',
                'dd-mm-yyyy' => 'dd-mm-yyyy',
                'dd.mm.yyyy' => 'dd.mm.yyyy'
            ];
        }

        if ($settingKey === 'urlScheme') {
            return [
                'http' => 'http',
                'https' => 'https (Secure)'
            ];
        }

        return[];
    }

    /**
     * Get Collection Options
     *
     * Get all collection summary rows
     * @param  void
     * @return mixed Array | null
     */
    public function getCollections()
    {
        $dataMapper = $this->container->dataMapper;
        $CollectionMapper = $dataMapper('CollectionMapper');

        return $CollectionMapper->find();
    }
}
