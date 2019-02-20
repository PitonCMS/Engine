<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Models;

use Piton\ORM\DomainObject;

/**
 * Piton Page Value Object
 */
class Page extends DomainObject
{
    /**
     * Elements Array
     * @var array
     */
    public $elements = [];

    /**
     * Page Settings Array
     * @var array
     */
    public $settings = [];

    /**
     * Get Published Status
     *
     * @param void
     * @return string
     */
    public function getPublishedStatus()
    {
        $today = date('Y-m-d');

        if (empty($this->published_date)) {
            return 'draft';
        } elseif ($this->published_date > $today) {
            return 'pending';
        } elseif ($this->published_date <= $today) {
            return 'published';
        }

        return null;
    }
}
