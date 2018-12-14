<?php
/**
 * Error
 *
 * Extends Slim\Handlers\Error to support logging
 */
namespace Piton\Library;

use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Error extends \Slim\Handlers\Error
{
    /**
     * Logger
     * @var Object
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param object Logging instance
     */
    public function __construct($displayErrorDetails, Logger $logger)
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
     * @param \Exception             $exception The caught Exception object
     *
     * @return ResponseInterface
     * @throws UnexpectedValueException
     */
    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
        // Log the message
        $this->logger->critical('ERROR!');
        $this->logger->critical($exception->getMessage() . ' ' . $exception->getTraceAsString());

        return parent::__invoke($request, $response, $exception);
    }
}
