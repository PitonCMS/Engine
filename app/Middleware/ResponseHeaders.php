<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2021 - 2026 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Middleware;

use Piton\Library\Config;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface as Logger;

/*
 * Set Dynamic Response Headers
 */
class ResponseHeaders
{
    /**
     * @var Config
     */
    protected Config $config;

    /**
     * Logging Object
     * @var Psr\Log\LoggerInterface
     */
    protected Logger $logger;

    /**
     * Constructor
     *
     * @param Config $config Configuration settings from the container
     * @param Logger $logger
     */
    public function __construct(Config $config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        // Log instantiation
        $this->logger->debug('ResponseHeaders middleware LOADED at ' . time());
    }

    /**
     * Callable
     *
     * @param  Request $request
     * @param  RequestHandler $handler
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Log invocation
        $this->logger->debug('ResponseHeaders middleware INVOKED at ' . time());

        // This is an Exit middleware method so wait until exiting and get next request first
        $response = $handler->handle($request);

        // Get headers from settings
        $headers = $this->config['header'] ?? [];

        foreach ($headers as $header => $value) {
            // If header value is empty or falsey do not set header and skip iteration
            if (empty($value) || !$value) {
                continue;
            }

            // If the header value contains the string 'nonce' then expand to the current nonce base64 key
            if (mb_strpos($value, 'nonce') !== false) {
                $value = str_replace('nonce', 'nonce-' . $this->config['environment']['cspNonce'], $value);
            }

            // Set header, except for Strict-Transport-Security (STS)
            if ($header !== 'Strict-Transport-Security') {
                $response = $response->withHeader($header, "$value");
            }

            // Only set STS if NOT on localhost as this header will force future requests to HTTPS and give you a localhost headache
            if ($header === 'Strict-Transport-Security' && mb_strtolower($request->getUri()->getHost()) !== 'localhost') {
                $response = $response->withHeader($header, "$value");
            }
        }

        return $response;
    }
}
