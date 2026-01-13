<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

namespace Piton\Models\Entities;

use Piton\ORM\DomainObject;
use RuntimeException;

/**
 * Piton Entity Value Object
 */
class PitonEntity extends DomainObject
{
    // Define common WHO properties that most tables have
    protected ?int $created_by = null;
    protected ?string $created_date = null;
    protected ?int $updated_by = null;
    protected ?string $updated_date = null;

    /**
     * Get Object Property
     *
     * Returns class property value.
     * If there is no immediate match, then tries to convert camelCase $key to underscore $key to find a match
     * @param string $key Property name to get
     * @return mixed Property value
     */
    public function __get($key)
    {
        $propertyKey = property_exists($this, $key) ? $key : $this->convertCamelCaseToUnderScores($key);

        return $this->$propertyKey ?? null;
    }

    /**
     * Set Object Property
     *
     * Will throw an exception if property does not exist.
     * @param  string $key   Property key
     * @param  mixed  $value Property value to set
     * @throws RuntimeException if property does not exist
     */
    public function __set(string $key, mixed $value = null)
    {
        // Try original key first, then snake_case version
        $propertyKey = property_exists($this, $key) ? $key : $this->convertCamelCaseToUnderScores($key);

        if (!property_exists($this, $propertyKey)) {
            throw new RuntimeException("Piton Exception: Attempt to set value on non-existent property '{$key}' in " . get_class($this));
        }

        // Cast value to property type
        $value = $this->castValueToPropertyType($propertyKey, $value);

        $this->$propertyKey = $value;
        $this->setPropertyAsModified($propertyKey);
    }

    /**
     * Isset Properties
     *
     * This is allows Twig to use non-existent camelCase equivalents in templates
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return property_exists($this, $key) || property_exists($this, $this->convertCamelCaseToUnderScores($key));
    }

    /**
     * Convert Camel Case to Under Score Property key
     *
     * Converts camelCase property values to underscores and returns the new string
     * @param string $key
     * @return string
     */
    private function convertCamelCaseToUnderScores($key): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
    }
}
