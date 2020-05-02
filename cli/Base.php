<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2020 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\CLI;

use Psr\Container\ContainerInterface;

/**
 * Piton Base CLI Class
 *
 */
class Base
{
    /**
     * Container
     * @var Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Print Output
     *
     * Prints output with a trailing new line
     * @param $input
     * @return void
     */
    public function print($input)
    {
        if (is_string($input)) {
            echo $input;
        } elseif (!is_string($input)) {
            print_r($input);
        }

        echo "\n";
    }
}
