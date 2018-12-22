<?php
/**
 * Piton Base Controller
 *
 * All other controllers should extend this class.
 */
namespace Piton\Controllers;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
     * Page Data
     * @var array
     */
    protected $pageData = [];

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
    }

    /**
     * Render Template
     *
     * @param string $layout Path to layout
     * @param mixed  $data   Data to echo, Domain object or array
     */
    public function render($layout, $data = null)
    {
        $this->pageData['page'] = $data;
        return $this->container->view->render($this->response, $layout, $this->pageData);
    }

    /**
     * Redirect
     *
     * @param string $name Route name
     * @param array  $args Associative array of route arguments
     */
    public function redirect($routeName, $args = [])
    {
        // Save any alert messages in flash data
        if (isset($this->pageData['alert'])) {
            $Session = $this->container->sessionHandler;
            $Session->setFlashData('alert', $this->pageData['alert']);
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
