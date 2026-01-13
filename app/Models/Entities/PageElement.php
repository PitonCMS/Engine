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

/**
 * Piton Page Element Value Object
 */
class PageElement extends PitonEntity
{
    // Class Properties
    protected ?int $page_id = null;
    protected ?string $block_key = null;
    protected ?string $template = null;
    protected ?int $element_sort = null;
    protected ?string $title = null;
    protected ?string $content = null;
    protected ?string $excerpt = null;
    protected ?int $collection_id = null;
    protected ?int $gallery_id = null;
    protected ?int $media_id = null;
    protected ?string $embedded = null;

    // Derived properties
    protected ?object $definition = null;
    protected array $settings = [];

    // Media properties
    protected ?string $media_filename = null;
    protected ?string $media_width = null;
    protected ?string $media_height = null;
    protected ?string $media_feature = null;
    protected ?string $media_caption = null;
    protected ?Media $media = null;

    /**
     * Constructor
     *
     * @param ?array $row Data array from query
     */
    public function __construct(?array $row)
    {
        // Load properties
        parent::__construct($row);

        // Test if this includes a valid media reference, and create new Media object and assign as sub-object
        if (isset($this->media_filename)) {
            $mediaData = [];
            $mediaData['id'] = $this->media_id;
            $mediaData['filename'] = $this->media_filename;
            $mediaData['width'] = $this->media_width;
            $mediaData['height'] = $this->media_height;
            $mediaData['feature'] = $this->media_feature;
            $mediaData['caption'] = $this->media_caption;

            $this->media = new Media($mediaData);
        }

        // Remove media properties from page element object to clean up
        // Do not unset $this->media_id, needed to maintain foreign key reference
        unset($this->media_filename);
        unset($this->media_width);
        unset($this->media_height);
        unset($this->media_feature);
        unset($this->media_caption);
    }

    /**
     * Set Page Element Settings
     *
     * Filters array of data_store settings on element category and creates key:value array on $this->settings
     * @param ?array
     * @return void
     */
    public function setElementSettings(?array $settings): void
    {
        if (empty($settings)) {
            return;
        }

        array_walk($settings, function ($setting) {
            if ($setting->category === 'element' && $this->id === $setting->element_id) {
                $this->settings[$setting->setting_key] = $setting->setting_value;
            }
        });
    }
}
