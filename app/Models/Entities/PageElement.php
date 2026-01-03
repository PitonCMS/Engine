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
    protected ?string $element_sort = null;
    protected ?string $title = null;
    protected ?string $content = null;
    protected ?string $excerpt = null;
    protected ?int $collection_id = null;
    protected ?int $gallery_id = null;
    protected ?int $media_id = null;
    protected ?string $embedded = null;

    // Query properties
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
        $this->page_id = isset($row['page_id']) ? (int) $row['page_id'] : null;
        $this->block_key = isset($row['block_key']) ? $row['block_key'] : null;
        $this->template = isset($row['template']) ? $row['template'] : null;
        $this->element_sort = isset($row['element_sort']) ? $row['element_sort'] : null;
        $this->title = isset($row['title']) ? $row['title'] : null;
        $this->content = isset($row['content']) ? $row['content'] : null;
        $this->excerpt = isset($row['excerpt']) ? $row['excerpt'] : null;
        $this->collection_id = isset($row['collection_id']) ? (int) $row['collection_id'] : null;
        $this->gallery_id = isset($row['gallery_id']) ? (int) $row['gallery_id'] : null;
        $this->media_id = isset($row['media_id']) ? (int) $row['media_id'] : null;
        $this->embedded = isset($row['embedded']) ? $row['embedded'] : null;

        // Test if this includes a media reference, and create new Media object and assign as sub-object
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

        // Remove media properties from page element object
        // unset($this->media_id);
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
     * @param array|null
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
