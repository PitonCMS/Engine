<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

namespace Piton\Middleware;

use Closure;
use Piton\Library\Config;
use Piton\Library\Handlers\CsrfGuard;
use Piton\Library\Handlers\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface as Logger;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;

/*
 * Merge site settings from database into app settings in the Container
 */
class LoadSiteSettings
{
    /**
     * Class Properties
     */
    protected Closure $dataMapper;
    protected CsrfGuard $csrfGuardHandler;
    protected Session $sessionHandler;
    protected Config $appSettings;
    protected array $newSettings;
    protected Logger $logger;
    private RouteResolverInterface $routeResolver;
    private RouteCollectorInterface $routeCollector;

    /**
     * Constructor
     *
     * @param Config $config
     * @param Closure $dataMapper
     * @param CsrfGuard $csrfGuardHandler
     * @param Session $sessionHandler
     * @param RouteResolverInterface $routeResolver
     * @param RouteCollectorInterface $routeCollector
     * @param Logger $logger
     */
    public function __construct(
        Config $config,
        Closure $dataMapper,
        CsrfGuard $csrfGuardHandler,
        Session $sessionHandler,
        RouteResolverInterface $routeResolver,
        RouteCollectorInterface $routeCollector,
        Logger $logger
    ) {
        $this->appSettings = $config;
        $this->dataMapper = $dataMapper;
        $this->csrfGuardHandler = $csrfGuardHandler;
        $this->sessionHandler = $sessionHandler;
        $this->routeResolver = $routeResolver;
        $this->routeCollector = $routeCollector;
        $this->logger = $logger;

        $this->newSettings['environment'] = [];
        $this->newSettings['site'] = [];

        // Log instantiation
        $this->logger->debug('LoadSiteSettings middleware LOADED at ' . time());
    }

    /**
     * Callable
     *
     * @param  Request  $request  PSR7 request
     * @param  RequestHandler $handler
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Log invocation
        $this->logger->debug('LoadSiteSettings middleware INVOKED at ' . time());

        $this->loadDatabaseSettings();
        $this->loadConfigSettings($request);

        // Merge settings from database into application settings
        $this->appSettings->merge($this->newSettings);

        // Next Middleware call
        return $handler->handle($request);
    }

    /**
     * Load Database Settings
     *
     * @param  void
     * @return void
     */
    protected function loadDatabaseSettings(): void
    {
        $dataStoreMapper = (($this->dataMapper)('DataStoreMapper'));
        $siteSettings = $dataStoreMapper->findSiteSettings() ?? [];

        // Create new multi-dimensional array of 'environment' (piton) and 'site' (other category) settings
        foreach ($siteSettings as $row) {
            if ($row->category === 'piton') {
                $this->newSettings['environment'][$row->setting_key] = $row->setting_value;
            } else {
                $this->newSettings['site'][$row->setting_key] = $row->setting_value;
            }
        }
    }

    /**
     * Load Config Settings
     *
     * Set config file and other dynamic settings
     * @param  Request  $request  PSR7 request
     * @return void
     */
    protected function loadConfigSettings(Request $request): void
    {
        // Copy production flag from config file to keep it in the new settings array
        $this->newSettings['environment']['production'] = $this->appSettings['environment']['production'];

        // Generate Content Security Policy nonce
        $this->newSettings['environment']['cspNonce'] = base64_encode(random_bytes(16));

        // Load piton engine version from composer.lock
        if (null !== $definition = json_decode(file_get_contents(ROOT_DIR . 'composer.lock'))) {
            $engineKey = array_search('pitoncms/engine', array_column($definition->packages, 'name'));
            $this->newSettings['environment']['engine'] = $definition->packages[$engineKey]->version;
            $this->newSettings['environment']['commit'] = isset($definition->packages[$engineKey]->source) ? $definition->packages[$engineKey]->source->reference : null;
        }

        // Get current route name. This is a bit hacky, as we are trying to get the route context very early before routing has loaded
        $routingResults = $this->routeResolver->computeRoutingResults(
            $request->getUri()->getPath(),
            $request->getMethod()
        );

        $routeIdentifier = $routingResults->getRouteIdentifier();
        $routeName = null;

        if ($routeIdentifier !== null) {
            $route = $this->routeCollector->lookupRoute($routeIdentifier);
            $routeName = $route->getName();
        }

        $this->newSettings['environment']['currentRouteName'] = $routeName;

        // This is used to break the cache by appending to asset files as a get param
        $this->newSettings['environment']['assetVersion'] =
            ($this->newSettings['environment']['production']) ? $this->newSettings['environment']['engine'] : date('U');

        // Add CSRF Token and Value to environment array
        $this->newSettings['environment']['csrfTokenName'] = $this->csrfGuardHandler->getTokenName();
        $this->newSettings['environment']['csrfTokenValue'] = $this->csrfGuardHandler->getTokenValue();
        // $this->newSettings['environment']['csrfHeaderName'] = $this->csrfGuardHandler->getHeaderName();

        // Set current project directory
        $this->newSettings['environment']['projectDir'] = basename(ROOT_DIR);

        // Set session user info
        $this->newSettings['environment']['sessionUserId'] = $this->sessionHandler->getData('user_id');
        $this->newSettings['environment']['sessionUserFirstName'] = $this->sessionHandler->getData('first_name');
        $this->newSettings['environment']['sessionUserLastName'] = $this->sessionHandler->getData('last_name');
    }
}
