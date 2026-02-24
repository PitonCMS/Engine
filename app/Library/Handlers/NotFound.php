<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

namespace Piton\Library\Handlers;

use Piton\Library\Twig\Base;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpNotFoundException;
use Throwable;

/**
 * Piton Not Found Handler
 */
class NotFound
{
    /**
     * Response Factory
     *
     * @var Psr\Http\Message\ResponseFactoryInterface
     */
    protected ResponseFactoryInterface $responseFactory;

    /**
     * Container
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * Twig View Handler
     *
     * @var \Slim\Views\Twig
     */
    protected $view;

    /**
     * Constructor
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param ContainerInterface       $container PSR Container
     */
    public function __construct(ResponseFactoryInterface $responseFactory, ContainerInterface $container)
    {
        $this->responseFactory = $responseFactory;
        $this->container = $container;
        $this->view = $container->get('view');
    }

    /**
     * Invoke not found handler as callable
     *
     * Adds logging for not found pages
     * Determines public or admin facing 404 page template path
     * @param  Request  $request
     * @param  RequestHandler Middleware handler
     * @param  bool $displayErrorDetails (Not used, set in bootstrap)
     * @param  bool $logErrors (Not used, set in bootstrap)
     * @param  bool $logErrorDetails (Not used, set in bootstrap)
     * @return Response
     */
    public function __invoke(
        Request $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): Response {
        // Only handle 404 errors
        if ($exception instanceof HttpNotFoundException) {
            // Get request URL to determine if this was thrown in /admin or on the public site
            $path = $request->getUri()->getPath();

            // Create response
            $response = $this->responseFactory->createResponse(404);

            // If request is for a file or image then just return the 404 status and stop
            if (preg_match('/^.*\.(jpe?g|png|gif|css|js|map|ico|txt|svg)$/i', $path)) {
                return $response;
            }

            // Load Piton Twig Extension
            $this->view->addExtension(new Base($request, $this->container));

            // Render not found template and return
            $templateString = $this->renderHtmlNotFoundOutput();
            $body = $response->getBody();
            $body->write($templateString);

            return $response;
        }

        // Rethrow other errors
        throw $exception;
    }

    /**
     * Return a custom not found page
     *
     * @param  Request  $request  The most recent Request object
     * @return string
     */
    protected function renderHtmlNotFoundOutput(): string
    {
        $pageTitle = ['page' => ['title' => 'Not Found']];

        // Set theme notFound template path
        if (file_exists(ROOT_DIR . 'structure/templates/system/notFound.html')) {
            return $this->view->fetch('system/notFound.html', $pageTitle);
        }

        // Otherwise return default template
        return $this->view->fetch('@admin/system/notFound.html', $pageTitle);
    }
}
