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
     * @param string $template Path to template
     * @param mixed  $data   Data to echo, Domain object or array
     */
    protected function render($template, $data = null)
    {
        $this->pageData['page'] = $data;
        return $this->container->view->render($this->response, $template, $this->pageData);
    }

    /**
     * Redirect
     *
     * @param string $name Route name
     * @param array  $args Associative array of route arguments
     */
    protected function redirect($routeName, $args = [])
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
