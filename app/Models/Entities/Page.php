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
 * Piton Page Value Object
 */
class Page extends PitonEntity
{
    // Class properties
    protected ?int $collection_id = null;
    protected ?string $collection_slug = null;
    protected ?string $collection_title = null;
    protected ?string $first_name = null;
    protected ?string $last_name = null;
    protected ?string $author = null;
    protected ?string $first_element_content = null;
    protected ?string $page_slug = null;
    protected ?string $template = null;
    protected ?string $title = null;
    protected ?string $sub_title = null;
    protected ?string $meta_description = null;
    protected ?string $published_date = null;
    protected ?int $view_count = null;
    protected ?int $media_id = null;
    protected ?string $media_filename;
    protected ?string $media_width;
    protected ?string $media_height;
    protected ?string $media_feature;
    protected ?string $media_caption;
    protected ?Media $media = null;
    protected array $blocks = [];
    protected array $settings = [];

    /**
     * Constructor
     *
     * @param ?array $row Data array from query
     */
    public function __construct(?array $row)
    {
        // Assign properties
        $this->collection_id = isset($row['collection_id']) ? (int) $row['collection_id'] : null;
        $this->collection_slug = isset($row['collection_slug']) ? $row['collection_slug'] : null;
        $this->collection_title = isset($row['collection_title']) ? $row['collection_title'] : null;
        $this->first_name = isset($row['first_name']) ? $row['first_name'] : null;
        $this->last_name = isset($row['last_name']) ? $row['last_name'] : null;
        $this->author = isset($row['author']) ? $row['author'] : null;
        $this->first_element_content = isset($row['first_element_content']) ? $row['first_element_content'] : null;
        $this->page_slug = isset($row['page_slug']) ? $row['page_slug'] : null;
        $this->template = isset($row['template']) ? $row['template'] : null;
        $this->title = isset($row['title']) ? $row['title'] : null;
        $this->sub_title = isset($row['sub_title']) ? $row['sub_title'] : null;
        $this->meta_description = isset($row['meta_description']) ? $row['meta_description'] : null;
        $this->published_date = isset($row['published_date']) ? $row['published_date'] : null;
        $this->view_count = isset($row['view_count']) ? (int) $row['view_count'] : null;

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
     * Set Block Elements
     *
     * Accepts array of element objects and converts to multidimensional array of
     * [block_key][] element objects and assigns to $this->blocks
     * @param array|null $elements
     * @return void
     */
    public function setBlockElements(?array $elements): void
    {
        // Nothing to do with an empty array but stop
        if (empty($elements)) {
            return;
        }

        // Go through array and assign by block key as index
        array_walk($elements, function ($el) {
            if ($this->id === $el->page_id) {
                $this->blocks[$el->block_key][] = $el;
            }
        });
    }

    /**
     * Set Page Settings
     *
     * Filters array of data_store settings on page category and creates key:value array on $this->settings
     * @param array|null
     * @return void
     */
    public function setPageSettings(?array $settings): void
    {
        if (empty($settings)) {
            return;
        }

        array_walk($settings, function ($setting) {
            if ($setting->category === 'page' && $this->id === $setting->page_id) {
                $this->settings[$setting->setting_key] = $setting->setting_value;
            }
        });
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
