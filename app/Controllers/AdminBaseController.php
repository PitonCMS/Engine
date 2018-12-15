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
     * Set Flash Alert
     *
     * Set alert using flash data to session
     * @param string Severity
     * @param string Heading (Optional)
     * @param string Message (Optional)
     * @return void
     */
    public function setAlert($severity, $heading = null, $message = null)
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

        $session->setFlashData('alert', $alert);

        return;
    }
}
