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
 * Piton Page Element Media Value Object
 */
class PageElementMedia extends PitonEntity
{
    public $media;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (isset($this->image_path)) {
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

        // Remove media properties
        unset($this->media_id);
        unset($this->media_filename);
        unset($this->media_width);
        unset($this->media_height);
        unset($this->media_feature);
        unset($this->media_caption);
        unset($this->image_path);
    }
}
