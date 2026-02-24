<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
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
    protected ?string $page_slug = null;
    protected ?string $template = null;
    protected ?string $title = null;
    protected ?string $sub_title = null;
    protected ?string $meta_description = null;
    protected ?string $published_date = null;
    protected ?int $media_id = null;
    protected ?int $view_count = null;

    // Derived properties
    protected ?string $collection_slug = null;
    protected ?string $collection_title = null;
    protected ?string $first_name = null;
    protected ?string $last_name = null;
    protected ?string $author = null;
    protected ?string $first_element_content = null;
    protected ?string $template_name = null;
    protected ?object $definition = null;
    protected ?string $collection_definition = null;

    // Media properties
    protected ?string $media_filename = null;
    protected ?string $media_width = null;
    protected ?string $media_height = null;
    protected ?string $media_feature = null;
    protected ?string $media_caption = null;
    protected ?Media $media = null;

    // Sub elements
    protected array $blocks = [];
    protected array $settings = [];

    /**
     * Constructor
     *
     * @param ?array $row Data array from query
     */
    public function __construct(?array $row)
    {
        // Load properties
        parent::__construct($row);

        // Test if this data set includes a valid media reference, and then create new Media object and assign as sub-object
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
