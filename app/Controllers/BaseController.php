<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 - 2026 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Controllers;

use Exception;
use Piton\Library\Twig\Base;
use Piton\Pagination\TwigPagination;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Piton Base Controller
 *
 * All other controllers should extend this class.
 */
class BaseController
{
    // Controller properties
    protected ContainerInterface $container;
    protected Request $request;
    protected Response $response;
    protected $view;
    protected array $alert = [];
    protected array $settings = [];

    /**
     * Constructor
     *
     * @param Request $request
     * @param Response $response
     * @param ContainerInterface $container
     */
    public function __construct(Request $request, Response $response, ContainerInterface $container)
    {
        $this->request = $request;
        $this->response = $response;
        $this->container = $container;
        $this->view = $this->container->get('view');
        $this->settings['site'] = $container->get('settings')['site'];
        $this->settings['environment'] = $container->get('settings')['environment'];

        // Load Piton Twig files and Piton Pagination as Twig Extensions
        $this->pitonViewExtensions();
    }

    /**
     * Render Template
     *
     * @param string $template Path to template
     * @param mixed  $data     Data to echo, Domain object or array
     * @return Response
     */
    protected function render(string $template, $data = null): Response
    {
        // $twigView = $this->container->get('view');

        // By making page data a Twig Global, we can access page data in block elements which are loaded by a Twig function in the templates
        $twigEnvironment = $this->view->getEnvironment();
        $twigEnvironment->addGlobal('page', $data);

        // Add application alert messages as a global to display in the template within this request
        $twigEnvironment->addGlobal('alert', $this->alert);

        return $this->view->render($this->response, $template);
    }

    /**
     * Redirect
     *
     * @param string $name Route name to redirect to
     * @param array  $args Associative array of route arguments
     * @param int    $status Response status code, defaults to 302 Temporary
     * @return Response
     */
    protected function redirect(string $routeName, array $args = [], $status = 302): Response
    {
        // Save any alert messages to session flash data for next request
        if (isset($this->alert)) {
            $session = $this->container->get('sessionHandler');
            $session->setFlashData('alert', $this->alert);
        }

        return $this->response->withHeader('Location', $this->container->get('router')->pathFor($routeName, $args))->withStatus($status);
    }

    /**
     * Show Page Not Found (404)
     *
     * Returns http status 404 Not Found and custom error template
     * @param void
     * @return Response
     */
    protected function notFound(): Response
    {
        $notFound = $this->container->get('notFoundHandler');

        return $notFound($this->request, $this->response);
    }

    /**
     * XHR Response
     *
     * Returns asynchronous response as application/json
     * @param  string $status  Status code "success"|"error"
     * @param  ?string $text    Document to sent
     * @return Response
     */
    protected function xhrResponse(string $status, ?string $text): Response
    {
        // Make sure $status is set to success or error
        if (!in_array($status, ['success', 'error'])) {
            throw new Exception("Invalid XHR Status Code");
        }

        $response = $this->response->withHeader('Content-Type', 'application/json');

        return $this->response->write(json_encode([
            "status" => $status,
            "text" => $text,
        ]));
    }

    /**
     * Get Pagination Object
     *
     * Returns Piton\Pagination\TwigPagination object from the Twig environment array of extensions
     * to allow update of runtime settings
     * @param void
     * @return TwigPagination
     */
    protected function getPagination(): TwigPagination
    {
        return $this->view->getEnvironment()->getExtensions()['Piton\Pagination\TwigPagination'];
    }

    /**
     * Instantiate Piton View Extensions
     */
    private function pitonViewExtensions(): void
    {
        // Piton Twig Extension
        $this->view->addExtension(new Base($this->request, $this->container));

        // Load Pagination with default results per page setting
        $this->view->addExtension(new TwigPagination(['resultsPerPage' => $this->container->get('settings')['pagination']['resultsPerPage']]));
    }
}
