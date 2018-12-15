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
     * @param string Path to layout
     * @param array|null Data to echo
     */
    public function render($layout, $data = null)
    {
        return parent::render('layouts/' . $layout, $data);
    }
}
