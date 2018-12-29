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
 * Piton Admin Base Controller
 *
 * All other admin controllers should extend this class.
 */
class AdminBaseController extends BaseController
{
    /**
     * Render Admin Template
     *
     * Modifies path to layout then calls parent render() method
     * @param string $layout Path to layout
     * @param mixed  $data   Data to echo, Domain object or array
     */
    public function render($layout, $data = null)
    {
        return parent::render('@admin/' . $layout, $data);
    }

    /**
     * Set Alert
     *
     * Set alert using flash data to session
     * @param string $severity Severity level
     * @param string $heading  Heading text
     * @param mixed  $messge   Message or array messages (Optional)
     * @return void
     */
    public function setAlert($severity, $heading, $message = null)
    {
        $session = $this->container->sessionHandler;

        // Make sure severity level is in our CSS
        $severityList = ['primary','secondary','success','danger','warning','info'];
        if (!in_array($severity, $severityList)) {
            throw new \Exception("Alert severity not found in list.");
        }

        $alert = [
            'severity' => $severity,
            'heading' => $heading,
            'message' => $message
        ];

        // Alert data is made available in the template, or if $this->redirect() is called
        // then data persists in flash data for one page view
        $this->pageData['alert'] = $alert;

        return;
    }
}
