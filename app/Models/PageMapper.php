<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Models;

use Piton\ORM\DataMapperAbstract;
use Exception;

/**
 * Piton Page Mapper
 */
class PageMapper extends DataMapperAbstract
{
    protected $table = 'page';
    protected $modifiableColumns = [
        'collection_slug',
        'page_slug',
        'definition',
        'template',
        'title',
        'sub_title',
        'meta_description',
        'published_date',
        'image_path'
    ];
    protected $domainObjectClass = __NAMESPACE__ . '\Entities\Page';

    /**
     * Find Published Page By Slug
     *
     * Finds published page by by slug, including collection detail page
     * @param string  $pageSlug       Page slug
     * @param string  $collectionSlug Collection slug
     * @return mixed                  Page object or null if not found
     */
    public function findPublishedPageBySlug(string $pageSlug, string $collectionSlug = null)
    {
        $this->makeSelect();
        $this->sql .= ' and page_slug = ?';
        $this->bindValues[] = $pageSlug;

        if (null === $collectionSlug) {
            $this->sql .= ' and collection_slug is null';
        } else {
            $this->sql .= ' and collection_slug = ?';
            $this->bindValues[] = $collectionSlug;
        }

        $this->sql .= " and published_date <= '{$this->today()}'";

        return $this->findRow();
    }

    /**
     * Find All Pages
     *
     * Finds all pages, does not include element data
     * Does not include collection detail pages
     * @param  bool  $includeUnpublished Filter on published pages
     * @return mixed                     Array | null
     */
    public function findPages(bool $includeUnpublished = false)
    {
        $this->makeSelect();
        $this->sql .= " and collection_slug is null";

        if (!$includeUnpublished) {
            $this->sql .= " and published_date <= '{$this->today()}'";
        }

        return $this->find();
    }

    /**
     * Find All Collection Detail Pages
     *
     * Finds all pages, does not include element data
     * @param  bool  $includeUnpublished Filter on unpublished pages
     * @return mixed                     Array | null
     */
    public function findCollectionPages(bool $includeUnpublished = false)
    {
        $this->makeSelect();
        $this->sql .= " and collection_slug is not null";

        if (!$includeUnpublished) {
            $this->sql .= " and published_date <= '{$this->today()}'";
        }

        $this->sql .= ' order by collection_slug';

        return $this->find();
    }

    /**
     * Find Collection Pages by Collection Slug
     *
     * Finds all related collection detail pages
     * @param  int   $collectionSlug
     * @param  bool  $includeUnpublished    Include unpublished collection pages
     * @return mixed                        Array | null
     */
    public function findCollectionPagesBySlug($collectionSlug, bool $includeUnpublished = false)
    {
        $this->makeSelect();
        $this->sql .= ' and collection_slug = ?';
        $this->bindValues[] = $collectionSlug;

        if (!$includeUnpublished) {
            $this->sql .= " and published_date <= '{$this->today()}'";
        }

        return $this->find();
    }

    /**
     * Find Collections
     *
     * Return list of collections
     */
    public function findCollections()
    {
        $this->sql = 'select distinct collection_slug from page where collection_slug is not null order by collection_slug';

        return $this->find();
    }
}
