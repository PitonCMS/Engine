<?php
/**
 * Piton Front End Base Controller
 *
 * All other front end controllers should extend this class.
 */
namespace Piton\Controllers;

class FrontBaseController extends BaseController
{
    /**
     * Render Front End Template
     *
     * Modifies path to layout then calls parent render() method
     * @param string $layout Path to layout
     * @param mixed  $data   Data to echo, Domain object or array
     */
    public function render($layout, $data = null)
    {
        return parent::render('layouts/' . $layout, $data);
    }

    /**
     * Build Page Elements by Section
     *
     * Takes array of page elements and builds multi-dimensional array of element objects
     * with section names as top level keys
     * @param array $elements Array of page element domain models
     * @return array
     */
    protected function buildElementsBySection($elements)
    {
        if (empty($elements)) {
            return $elements;
        }

        $output = [];
        foreach ($elements as $row) {
            $output[$row->section_name][] = $row;
        }

        return $output;
    }
}
