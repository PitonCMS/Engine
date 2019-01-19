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
        return parent::render('layouts/' . $template, $data);
    }

    /**
     * Build Page Elements by Block
     *
     * Takes array of page elements and builds multi-dimensional array of element objects
     * with block names as top level keys
     * @param array  $elements Array of page element domain models
     * @return array
     */
    protected function buildElementsByBlock($elements)
    {
        if (empty($elements)) {
            return $elements;
        }

        $output = [];
        foreach ($elements as $row) {
            $output[$row->block_key][] = $row;
        }

        return $output;
    }

    /**
     * Build Fields
     *
     * Takes array of page elements and builds multi-dimensional array of element objects
     * with block names as top level keys
     * @param array  $elements Array of page element domain models
     * @return array
     */
    protected function buildFields($fields)
    {
        if (empty($fields)) {
            return $fields;
        }

        $output = [];
        foreach ($fields as $row) {
            $output[$row->setting_key] = $row->setting_value;
        }

        return $output;
    }
}
