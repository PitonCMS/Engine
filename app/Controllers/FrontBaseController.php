<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Controllers;

/**
 * Piton Front End Base Controller
 *
 * All other front end controllers should extend this class.
 */
class FrontBaseController extends BaseController
{
    /**
     * Render Front End Template
     *
     * Modifies path to template then calls parent render() method
     * @param string $template Path to template
     * @param mixed  $data   Data to echo, Domain object or array
     */
    public function render($template, $data = null)
    {
        return parent::render('pages/' . $template, $data);
    }

   /**
     * Build Page Elements by Block
     *
     * Takes array of page elements and changes keys to use block->key as array keys
     * @param array  $elements Array of page element domain models
     * @return array
     */
    protected function buildElementsByBlock($elements)
    {
        if (empty($elements)) {
            return $elements;
        }

        $output = [];
        foreach ($elements as $element) {
            $output[$element->block_key][] = $element;
        }

        return $output;
    }

    /**
     * Build Page Settings
     *
     * Takes array of page settings and changes keys to use setting->key as array keys
     * @param array  $settings Array of page settings
     * @return array
     */
    protected function buildPageSettings($settings)
    {
        if (empty($settings)) {
            return $settings;
        }

        $output = [];
        foreach ($settings as $setting) {
            $output[$setting->setting_key] = $setting->setting_value;
        }

        return $output;
    }
}
