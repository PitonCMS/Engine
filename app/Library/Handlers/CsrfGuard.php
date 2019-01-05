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

    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->loadSessionToken();
    }

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

    public function getTokenName()
    {
        return $this->csrfTokenName;
    }

    public function getTokenValue()
    {
        return $this->csrfTokenValue;
    }

    protected function validateToken($token)
    {
        return hash_equals($this->csrfTokenValue, $token);
    }

    protected function loadSessionToken()
    {
        if (null === $this->csrfTokenValue = $this->session->getData($this->csrfTokenName)) {
            $this->csrfTokenValue = $this->generateToken();
            $this->session->setData($this->csrfTokenName, $this->csrfTokenValue);
        }
    }

    protected function generateToken()
    {
        return base64_encode(random_bytes(64));
    }
}
