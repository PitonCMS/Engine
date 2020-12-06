<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

/**
 * Load Middleware
 *
 * Once loaded, middleware is called by Slim in reverse order
 */
$app->add(new Piton\Middleware\LoadSiteSettings($container['dataMapper'], $container['settings']));
