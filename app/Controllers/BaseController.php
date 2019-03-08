<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Controllers;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Piton Base Controller
 *
 * All other controllers should extend this class.
 */
class BaseController
{
    /**
     * Container
     * @var Interop\Container\ContainerInterface
     */
    protected $container;

    /**
     * Request
     * @var Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    /**
     * Response
     * @var Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * Page Alerts
     * @var array
     */
    protected $alert = [];

    /**
     * Site Settings Array
     * @var array
     */
    protected $siteSettings = [];

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $container->request;
        $this->response = $container->response;
        $this->siteSettings = $container->get('settings')['site'];
    }

    /**
     * Render Template
     *
     * @param string $template Path to template
     * @param mixed  $data     Data to echo, Domain object or array
     */
    protected function render($template, $data = null)
    {
        $twigView = $this->container->view;

        // By making page data a Twig Global, we can access page data in block elements
        // which are loaded by a Twig function
        $twigEnvironment = $twigView->getEnvironment();
        $twigEnvironment->addGlobal('page', $data);
        $twigEnvironment->addGlobal('alert', $this->alert);

        return $twigView->render($this->response, $template);
    }

    /**
     * Redirect
     *
     * @param string $name Route name
     * @param array  $args Associative array of route arguments
     */
    protected function redirect($routeName, $args = [])
    {
        // Save any alert messages in session flash data
        if (isset($this->alert)) {
            $session = $this->container->sessionHandler;
            $session->setFlashData('alert', $this->alert);
        }

        return $this->response->withRedirect($this->container->router->pathFor($routeName, $args));
    }

    /**
     * Show Page Not Found (404)
     *
     * Returns http status 404 Not Found and custom error template
     */
    protected function notFound()
    {
        $notFound = $this->container->get('notFoundHandler');
        return $notFound($this->request, $this->response);
    }
}
