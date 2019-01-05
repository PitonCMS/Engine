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
     * Constructor
     *
     * @param  obj $session Piton\Library\Handlers\Session
     * @return void
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
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

                throw new Exception('Invalid CSRF Token');
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
}
