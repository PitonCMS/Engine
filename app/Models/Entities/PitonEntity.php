<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2026 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Models\Entities;

use Piton\ORM\DomainObject;

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
     * Constructor
     */
    public function __construct(?array $row)
    {
        $this->created_by = isset($row['created_by']) ? (int) $row['created_by'] : null;
        $this->created_date = isset($row['created_date']) ? $row['created_date'] : null;
        $this->updated_by = isset($row['updated_by']) ? (int) $row['updated_by'] : null;
        $this->updated_date = isset($row['updated_date']) ? $row['updated_date'] : null;

        parent::__construct($row);
    }

    /**
     * Get Object Property
     *
     * Returns class property. If there is no immediate match, then tries to convert camelCase $key to underscore $key to find a match
     * @param  string $key Property name to get
     * @return mixed Property value
     */
    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        } else {
            $underScoreKey = $this->convertCamelCaseToUnderScores($key);

            if (property_exists($this, $underScoreKey)) {
                return $this->$underScoreKey;
            }
        }

        return null;
    }

    /**
     * Set Object Property
     *
     * Will throw an exception if property does not exist.
     * @param  string $key   Property key
     * @param  mixed  $value Property value to set
     * @throws Throwable if property does not exist
     */
    public function __set(string $key, mixed $value = null)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        } else {
            // Try converting camelCase to underscore to find property
            $underScoreKey = $this->convertCamelCaseToUnderScores($key);

            if (property_exists($this, $underScoreKey)) {
                $this->$underScoreKey = $value;
            } else {
                throw new \RuntimeException("Piton Exception: Attempt to set value on non-existent property '{$key}' in " . get_class($this));
            }
        }
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
        if (property_exists($this, $key)) {
            return true;
        } else {
            return property_exists($this, $this->convertCamelCaseToUnderScores($key));
        }
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
