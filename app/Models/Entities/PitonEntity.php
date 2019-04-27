<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Models\Entities;

use Piton\ORM\DomainObject;

/**
 * Piton Entity Value Object
 */
class PitonEntity extends DomainObject
{
    /**
     * Piton Who Properties
     * @var mixed int | string
     */
    public $created_by;
    public $created_date;
    public $updated_by;
    public $updated_date;

    /**
     * Get Object Property
     *
     * The switch statement maps non-existent camelCase properties to real properties in database
     * @param  mixed $key Property name to get
     * @return mixed      Property value | null
     */
    public function __get($key)
    {
        switch ($key) {
            case "createdBy":
                return $this->created_by ?: null;
            case "createdDate":
                return $this->created_date ?: null;
            case "updatedBy":
                return $this->updated_by ?: null;
            case "updatedDate":
                return $this->updated_date ?: null;
        }

        return parent::__get($key);
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
        switch ($key) {
            case "createdBy":
            case "createdDate":
            case "updatedBy":
            case "updatedDate":
                return true;
        }

        return false;
    }
}
