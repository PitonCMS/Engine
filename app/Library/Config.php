<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

namespace Piton\Library;

use ArrayAccess;

/**
 * Piton Configuration Settings Value Object
 */
class Config implements ArrayAccess
{
    private array $data = [];

    /**
     * Constructor
     *
     * Accepts optional configuration settings array, which can also be loaded later using merge()
     * @param ?array $initial Array of settings to load
     */
    public function __construct(array $initial = [])
    {
        $this->data = $initial;
    }

    /**
     * Get a Config Value
     *
     * @param string $key    The top level array key for the desired setting
     * @param mixed $default Optional default value to return if no key is found
     * @return mixed         Returns the requested setting
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Set a Config Value
     *
     * Sets **OR REPLACES** a top-level setting element
     * If you are not sure, consider using merge() to load arrays, or merge an array into the settings
     * @param string $key   The array key to set/update
     * @param mixed  $value The value to store
     * @return void
     */
    public function set(string $key, mixed $value = null): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Merge Update Config Values
     *
     * Merge an array of values into the config
     * @param array $values
     * @return void
     */
    public function merge(array $values): void
    {
        $this->data = array_replace_recursive($this->data, $values);
    }

    /**
     * Return All Config Values
     * @param void
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /* ---------------------------------------------------------
     * ArrayAccess implementation
     * --------------------------------------------------------- */

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // If no key is provided, append like an array
        if ($offset === null) {
            $this->data[] = $value;

            return;
        }

        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }
}
