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
}
