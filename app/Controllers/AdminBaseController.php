<?php
/**
 * Piton Admin Base Controller
 *
 * All other admin controllers should extend this class.
 */
namespace Piton\Controllers;

class AdminBaseController extends BaseController
{
    /**
     * Render Admin Template
     *
     * Modifies path to layout then calls parent render() method
     * @param string Path to layout
     * @param array|null Data to echo
     */
    public function render($layout, $data = null)
    {
        return parent::render('@admin/' . $layout, $data);
    }

    /**
     * Set Alert
     *
     * Set alert using flash data to session
     * @param string Severity
     * @param string Heading
     * @param string|array Message(s) (Optional)
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
