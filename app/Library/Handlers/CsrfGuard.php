<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

/**
 * Piton CSRF Guard
 *
 * Middleware CSRF Protection for POST routes
 */
class CsrfGuard
{
    /**
     * CSRF token name
     * @var string
     */
    protected $csrfTokenName = 'pitonCsrfToken';

    /**
     * CSRF token from session
     * @var string
     */
    protected $csrfTokenValue;

    /**
     * Session Storage
     * @var object Piton\Library\Handlers\Session
     */
    protected $session;

    /**
     * Logging Object
     * @var object
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param  obj $session Piton\Library\Handlers\Session
     * $param  obj $logger  Logging object
     * @return void
     */
    public function __construct(Session $session, $logger)
    {
        $this->session = $session;
        $this->logger = $logger;
        $this->loadSessionToken();
    }

    /**
     * Invoke CSRF Guard
     *
     * Invoked on designated POST routes
     * @param  obj $request Psr\Http\Message\ServerRequestInterface
     * @param  obj $respose Psr\Http\Message\ResponseInterface
     * @param  callable     Next middleware to run
     * @return mixed        Callable, or HTTP 403 Error
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        // Validate POST request
        if ($request->getMethod() === 'POST') {
            $token = $request->getParsedBodyParam($this->csrfTokenName);

            if ($token === null || !$this->validateToken($token)) {
                // Bad token. Clear and reset
                $this->csrfTokenValue = null;
                $this->loadSessionToken();
                $this->logger->alert('PitonCMS: 403 Forbidden request, CSRF token mismatch');

                return $this->forbidden($request, $response);
            }
        }

        return $next($request, $response);
    }

    /**
     * Get Token Name
     *
     * @param  void
     * @return str
     */
    public function getTokenName()
    {
        return $this->csrfTokenName;
    }

    /**
     * Get Token Value
     *
     * @param  void
     * @return str
     */
    public function getTokenValue()
    {
        return $this->csrfTokenValue;
    }

    /**
     * Unset Token
     *
     * Remove token key and value from session
     * @param  void
     * @return void
     */
    public function unsetToken()
    {
        $this->session->unsetData($this->csrfTokenName);
    }

    /**
     * Validate Token
     *
     * Uses hash_equals() to compare saved token ($this->csrfTokenValue) with provided token
     * @param  str $token Token hash to comapre
     * @return bool
     */
    protected function validateToken($token)
    {
        return hash_equals($this->csrfTokenValue, $token);
    }

    /**
     * Load Token
     *
     * Loads token saved in session storage, or if not found then creates a new token and sets in session
     * @param  void
     * @return void
     */
    protected function loadSessionToken()
    {
        if (null === $this->csrfTokenValue = $this->session->getData($this->csrfTokenName)) {
            $this->csrfTokenValue = $this->generateToken();
            $this->session->setData($this->csrfTokenName, $this->csrfTokenValue);
        }
    }

    /**
     * Generate New Token
     *
     * Creates new token value
     * @param  void
     * @return void
     */
    protected function generateToken()
    {
        return base64_encode(random_bytes(64));
    }

    /**
     * Forbidden HTTP 403 Response
     *
     * Code borrowed and modified from Slim Error
     * Respond with HTTP 403 Forbidden
     * @param ServerRequestInterface $request   The most recent Request object
     * @param ResponseInterface      $response  The most recent Response object
     * @return HTTP 403 Forbidden
     */
    public function forbidden(ServerRequestInterface $request, ResponseInterface $response)
    {
        $contentType = $this->determineContentType($request);
        switch ($contentType) {
            case 'application/json':
                $output = $this->renderJsonErrorMessage();
                break;

            case 'text/html':
                $output = $this->renderHtmlErrorMessage();
                break;

            default:
                throw new Exception('Cannot render unknown content type ' . $contentType);
        }

        return $response
                ->withStatus(403)
                ->withHeader('Content-type', $contentType)
                ->write($output);
    }

    /**
     * Determine Request Content Type
     *
     * Code borrowed and modified from Slim Error
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function determineContentType(ServerRequestInterface $request)
    {
        $knownContentTypes = [
            'application/json',
            'text/html',
        ];

        $acceptHeader = $request->getHeaderLine('Accept');
        $selectedContentTypes = array_intersect(explode(',', $acceptHeader), $knownContentTypes);

        if (count($selectedContentTypes)) {
            return current($selectedContentTypes);
        }

        // Handle +json and +xml specially
        if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
            $mediaType = 'application/' . $matches[1];
            if (in_array($mediaType, $knownContentTypes)) {
                return $mediaType;
            }
        }

        return 'text/html';
    }

    /**
     * Render HTML 403 Forbidden page
     *
     * Code borrowed and modified from Slim Error
     * @return string
     */
    protected function renderHtmlErrorMessage()
    {
        $title = 'Piton 403 Forbidden Error';
        $html = '<p>This request is forbidden.</p>';

        $output = sprintf(
            "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
            "<title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana," .
            "sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{" .
            "display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s</body></html>",
            $title,
            $title,
            $html
        );

        return $output;
    }

    /**
     * Render JSON 403 Forbidden page
     *
     * Code borrowed and modified from Slim Error
     * @return string
     */
    protected function renderJsonErrorMessage()
    {
        $error = [
            'message' => 'Piton 403 Forbidden Error',
        ];

        return json_encode($error, JSON_PRETTY_PRINT);
    }
}
