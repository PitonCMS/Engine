<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 - 2026 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Library\Handlers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\log\LoggerInterface as Logger;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;
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
     * Twig View Handler
     *
     * @var \Slim\Views\Twig
     */
    protected $view;

    /**
     * Monolog Logger
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Constructor
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param Twig   $view   Slim Twig view handler
     * @param Logger $logger Logging
     */
    public function __construct(ResponseFactoryInterface $responseFactory, Twig $view, Logger $logger)
    {
        $this->responseFactory = $responseFactory;
        $this->view = $view;
        $this->logger = $logger;
    }

    /**
     * Invoke not found handler as callable
     *
     * Adds logging for not found pages
     * Determines public or admin facing 404 page template path
     * @param  Request  $request
     * @param  RequestHandler Middleware handler
     * @return Response
     */

    // public function __invoke(Request $request, RequestHandler $handler): Response

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

            // Log 404 request
            $this->logger->error("Not Found (404): {$request->getMethod()} $path");

            // Create response
            $response = $this->responseFactory->createResponse(404);

            // If request is for a file or image then just return the 404 status and stop
            if (preg_match('/^.*\.(jpe?g|png|gif|css|js|map|ico|txt|svg)$/i', $path)) {
                return $response;
            }

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
        // Set theme notFound template path
        if (file_exists(ROOT_DIR . 'structure/templates/system/notFound.html')) {
            return $this->view->fetch('system/notFound.html');
        }

        // Otherwise return default template
        return $this->view->fetch('@admin/system/notFound.html');
    }
}
