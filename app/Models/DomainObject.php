<?php
/**
 * Domain Model
 *
 * Base class for all domain models
 * Extend this class to include custom property handling
 */
namespace Piton\Models;

class DomainObject
{
    // This $id avoids an error when the __get() magic method in DomainObjectAbstract is called
    // on a non-existent property
    public $id;

    /**
     * Get Object Property
     *
     * Applies only to private and protected properties
     */
    public function __get($key)
    {
        return isset($this->$key) ?: false;
    }

    /**
     * Set Object Property
     *
     * Applies only to private and protected properties
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }
}
