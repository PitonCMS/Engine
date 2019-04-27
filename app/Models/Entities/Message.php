<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Models\Entities;

/**
 * Piton Message Value Object
 */
class Message extends PitonEntity
{
    /**
     * Message Properties
     */
    public $name;
    public $email;
    public $message;
    public $is_read;

    /**
     * Get Object Property
     *
     * The switch statement maps non-existent camelCase properties to real properties in database
     * @param  string $key Property name to get
     * @return mixed       Property value | null
     */
    public function __get($key)
    {
        switch ($key) {
            case 'isRead':
                return $this->is_read;
        }

        return parent::__get($key);
    }

    /**
     * Set Object Property
     *
     * The switch statement maps non-existent camelCase properties to real properties in database
     * @param  string $key   Property name to set
     * @param  mixed  $value Property value to set
     * @return void
     */
    public function __set($key, $value)
    {
        switch ($key) {
            case 'isRead':
                $this->is_read = $value;
                return;
        }

        parent::__set($key, $value);
    }

    /**
     * Isset Properties
     *
     * This is allows Twig to use non-existent camelCase equivalents in templates
     * @param  string $key
     * @return boolean
     */
    public function __isset($key)
    {
        switch ($key) {
            case 'isRead':
                return true;
        }

        return parent::__isset($key);
    }
}
