<?php
/**
 * Load Middleware
 *
 * Once loaded, middleware is called by Slim in reverse order
 */

$app->add(new Piton\Middleware\LoadSiteSettings($container['dataMapper'], $container['settings']));
