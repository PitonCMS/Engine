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
            case 'collectionSlug':
                return $this->collection_slug;
            case 'pageSlug':
                return $this->page_slug;
            case 'subTitle':
                return $this->sub_title;
            case 'metaDescription':
                return $this->meta_description;
            case 'publishedDate':
                return $this->published_date;
            case 'imagePath':
                return $this->image_path;
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
            case 'collectionSlug':
                $this->collection_slug = $value;
                return;
            case 'pageSlug':
                $this->page_slug = $value;
                return;
            case 'subTitle':
                $this->sub_title = $value;
                return;
            case 'metaDescription':
                $this->meta_description = $value;
                return;
            case 'publishedDate':
                $this->published_date = $value;
                return;
            case 'imagePath':
                $this->image_path = $value;
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
            case 'collectionSlug':
                return true;
            case 'pageSlug':
                return true;
            case 'subTitle':
                return true;
            case 'metaDescription':
                return true;
            case 'publishedDate':
                return true;
            case 'imagePath':
                return true;
        }

        return parent::__isset($key);
    }
}
