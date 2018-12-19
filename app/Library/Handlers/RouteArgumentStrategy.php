<?php
/**
 * Piton Route Argument Strategy
 *
 * The default Slim route strategy passes the Request, Response, objects and route Arguments array to the callback.
 * However, in Piton the Request and Response objects are available in the Controllers as properties.
 * This route strategy passes only the URL route arguments to the controller.
 */
namespace Piton\Library\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;

/**
 * Piton route callback strategy with route parameters as an array of arguments.
 */
class RouteArgumentStrategy implements InvocationStrategyInterface
{
    /**
     * Invoke a route callable with request, response, and all route parameters
     * as an array of arguments.
     *
     * @param array|callable         $callable
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $routeArguments
     *
     * @return mixed
     */
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ) {
        foreach ($routeArguments as $k => $v) {
            $request = $request->withAttribute($k, $v);
        }

        return call_user_func($callable, $routeArguments);
    }
}
