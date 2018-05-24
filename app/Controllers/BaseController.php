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
     * Index Page Element Keys
     *
     * Accepts array of page element objects, and returns associative array using the page element name as key
     * @param array of Piton\Models\DomainObject
     * @return array
     */
    protected function indexPageElementKeys(array $elements)
    {
        $newArray = [];

        // Check if we have something to process
        if (empty($elements)) {
            return $elements;
        }

        // Create associative array
        foreach ($elements as $row) {
            $newArray[$row->name] = $row;
        }

        return $newArray;
    }
}
