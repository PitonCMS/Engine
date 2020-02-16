<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Models\Entities;

/**
 * Piton Page Value Object
 */
class Page extends PitonEntity
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
     * Returns draft|pending|published depending on published date compared to today
     * @param void
     * @return string
     */
    public function getPublishedStatus(): ?string
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
