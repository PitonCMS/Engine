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
     * Returns class property. If there is no immediate match, then tries to convert camelCase $key to underscore to find a match
     * @param  string $key Property name to get
     * @return ?mixed Property value
     */
    public function __get($key)
    {
        $propertyValue = parent::__get($key);

        if (!empty($propertyValue)) {
            return $propertyValue;
        }

        // Go to backup, and look for the key but using underscores
        return $this->getCamelCaseToUnderScores($key);
    }

    /**
     * Isset Properties
     *
     * This is allows Twig to use non-existent camelCase equivalents in templates
     * @param string $key
     * @return mixed
     */
    public function __isset($key)
    {
        return true;
        // return $this->getCamelCaseToUnderScores($key);
    }

    /**
     * Get Camel Case to Under Score Property Value
     *
     * Converts camelCase property values to underscores and checks if property exists
     * If it does, then adds the camelCase property to this object with a pointer to the under score equivalent
     * @param string $key
     * @return ?mixed
     */
    private function getCamelCaseToUnderScores($key)
    {
        // Split camelCase variables to underscores and see if there is a match to an existing property
        $propertyKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));

        return parent::__get($propertyKey);
    }
}
