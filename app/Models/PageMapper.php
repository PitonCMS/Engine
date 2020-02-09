<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Models;

use Piton\Models\Entities\PitonEntity;
use Piton\ORM\DataMapperAbstract;

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
     * @return PitonEntity|null
     */
    public function findPublishedPageBySlug(string $pageSlug, string $collectionSlug = null): ?PitonEntity
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

        $this->sql .= " and published_date <= '{$this->today}'";

        return $this->findRow();
    }

    /**
     * Find Collection Pages by Collection Slug
     *
     * Finds all related collection detail pages
     * @param  string   $collectionSlug
     * @param  bool  $includeUnpublished    Include unpublished collection pages
     * @return array|null
     */
    public function findCollectionPagesBySlug(string $collectionSlug, bool $includeUnpublished = false): ?array
    {
        $this->makeSelect();
        $this->sql .= ' and collection_slug = ?';
        $this->bindValues[] = $collectionSlug;

        if (!$includeUnpublished) {
            $this->sql .= " and published_date <= '{$this->today}'";
        }

        return $this->find();
    }

    /**
     * Find All Pages
     *
     * Gets all pages without elements
     * Does not include collection detail pages
     * @param  bool  $includeUnpublished Filter on published pages
     * @return array|null
     */
    public function findPages(bool $includeUnpublished = false): ?array
    {
        $this->makeSelect();
        $this->sql .= " and collection_slug is null";

        if (!$includeUnpublished) {
            $this->sql .= " and published_date <= '{$this->today}'";
        }

        return $this->find();
    }

    /**
     * Find All Collection Detail Pages
     *
     * Finds all pages, does not include element data
     * @param  bool  $includeUnpublished Filter on unpublished pages
     * @return array|null
     */
    public function findCollectionPages(bool $includeUnpublished = false): ?array
    {
        $this->makeSelect();
        $this->sql .= " and collection_slug is not null";

        if (!$includeUnpublished) {
            $this->sql .= " and published_date <= '{$this->today}'";
        }

        $this->sql .= ' order by collection_slug';

        return $this->find();
    }

    /**
     * Find Collections
     *
     * Return list of collections
     * @param void
     * @return array|null
     */
    public function findCollections(): ?array
    {
        $this->sql = 'select distinct collection_slug from page where collection_slug is not null order by collection_slug';

        return $this->find();
    }
}
