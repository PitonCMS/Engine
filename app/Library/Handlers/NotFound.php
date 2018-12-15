<?php
/**
 * Piton Not Found Handler
 *
 * Extends the Slim NotFound handler
 */
namespace Piton\Library\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class NotFound extends \Slim\Handlers\NotFound
{
    /**
     * Twig View Handler
     *
     * @var \Slim\Views\Twig
     */
    protected $view;

    /**
     * Monolog Logger
     *
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * Template Path
     *
     * @var string
     */
    protected $templatePath;

    /**
     * Constructor
     *
     * @param object Slim\Views\Twig Slim Twig view handler
     * @param object \Monolog\Logger Logging
     */
    public function __construct(\Slim\Views\Twig $view, \Monolog\Logger $logger)
    {
        $this->view = $view;
        $this->logger = $logger;
    }

    /**
     * Invoke not found handler as callable
     *
     * Adds logging for not found pages
     * Determines public or admin facing 404 page template path
     * @param  ServerRequestInterface $request  The most recent Request object
     * @param  ResponseInterface      $response The most recent Response object
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        // Get request URL to determine if this was thrown in /admin or on the public site
        $path = $request->getUri()->getPath();

        // Set notFound template path context
        $this->templatePath = 'core/notFound.html';
        if (explode('/', $path)[1] === 'admin') {
            $this->templatePath = '@admin/notFound.html';
        }

        // If request is for a file or image then just return the 404 status and stop
        if (preg_match('/^.*\.(jpg|jpeg|png|gif|css|js)$/i', $path)) {
            return $response->withStatus(404);
        }

        // Log 404 request
        $this->logger->info("Not Found (404): {$request->getMethod()} {$path}");

        // Return status 404 and template
        return parent::__invoke($request, $response);
    }

    /**
     * Return a custom not found page
     *
     * @param  ServerRequestInterface $request  The most recent Request object
     * @param  ResponseInterface      $response The most recent Response object
     *
     * @return ResponseInterface
     */
    protected function renderHtmlNotFoundOutput(ServerRequestInterface $request)
    {
        // Render and return temmplate as string
        return $this->view->fetch($this->templatePath);
    }
}
