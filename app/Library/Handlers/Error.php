<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Library\Handlers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface as Logger;
use Exception;

/**
 * Error Handler
 *
 * Extends Slim\Handlers\Error to support logging
 */
class Error extends \Slim\Handlers\Error
{
    /**
     * Logger
     * @var Logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param bool   $displayErrorDetails
     * @param Logger $logger Logging instance
     */
    public function __construct(bool $displayErrorDetails, Logger $logger)
    {
        $this->logger = $logger;
        parent::__construct($displayErrorDetails);
    }

    /**
     * Invoke error handler
     *
     * Logs error exceptions and then calls parent method
     * @param ServerRequestInterface $request   The most recent Request object
     * @param ResponseInterface      $response  The most recent Response object
     * @param Exception             $exception The caught Exception object
     *
     * @return ResponseInterface
     * @throws UnexpectedValueException
     */
    public function __invoke(Request $request, Response $response, Exception $exception): Response
    {
        // Log the message
        $this->logger->critical('ERROR!');
        $this->logger->critical($exception->getMessage() . ' ' . $exception->getTraceAsString());

        return parent::__invoke($request, $response, $exception);
    }
}
