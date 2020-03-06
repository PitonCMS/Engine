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
     * Media Sub-Object
     * @var PitonEntity
     */
    public $media;

    /**
     * Constructor
     */
    public function __construct()
    {
        // The class properties are set by PDO::FETCH_CLASS *before* the constructor is called.
        // This checks if a media file was joined in the query, and then builds a media sub-object.
        // Media constructor sets additional calculated properties based on the image.
        if (isset($this->media_filename)) {
            // Create new Media object and assign as sub-object
            $media = new Media();
            $media->id = $this->media_id;
            $media->filename = $this->media_filename;
            $media->width = $this->media_width;
            $media->height = $this->media_height;
            $media->feature = $this->media_feature;
            $media->caption = $this->media_caption;
            $media->__construct();
            $this->media = $media;
        }

        // Remove media properties from page element object
        // unset($this->media_id);
        unset($this->media_filename);
        unset($this->media_width);
        unset($this->media_height);
        unset($this->media_feature);
        unset($this->media_caption);
    }

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
