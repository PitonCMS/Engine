<?php
/**
 * Piton Base Controller
 *
 * All other controllers should extend this class.
 * Loads the Slim Container to $this->container
 */
namespace Piton\Controllers;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BaseController
{
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Show Page Not Found (404)
     *
     * Returns status 404 Not Found and custom template as a method to exending controllers
     * @param  ServerRequestInterface $request  The most recent Request object
     * @param  ResponseInterface      $response The most recent Response object
     */
    protected function notFound(ServerRequestInterface $request, ResponseInterface $response)
    {
        $notFound = $this->container->get('notFoundHandler');
        return $notFound($request, $response);
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
