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
use PDO;

/**
 * Piton Page Mapper
 */
class PageMapper extends DataMapperAbstract
{
    protected $table = 'page';
    protected $modifiableColumns = [
        'collection_id',
        'page_slug',
        'template',
        'title',
        'sub_title',
        'meta_description',
        'published_date',
        'media_id'
    ];
    protected $domainObjectClass = __NAMESPACE__ . '\Entities\Page';

    /**
     * Find Page by ID
     *
     * Override from parent class to allow use of table alias.
     * Find one table row using the primary key ID
     * @param  int   $id Primary key ID
     * @return PitonEntity|null
     */
    public function findById(int $id): ?PitonEntity
    {
        $this->makeSelect();

        $this->sql .= " and p.id = ?";
        $this->bindValues[] = $id;

        return $this->findRow();
    }

    /**
     * Find All Page Content
     *
     * Gets all pages and collection pages without elements
     * @param  string $status 'draft'|'pending'|'published'|'all'
     * @param  int  $limit
     * @param  int  $offset
     * @return array|null
     */
    public function findContent(string $status, int $limit = null, int $offset = null): ?array
    {
        $this->makeSelect(true);

        if ($status === 'published') {
            $this->sql .= " and p.published_date <= '{$this->today}'";
        } elseif ($status === 'pending') {
            $this->sql .= " and p.published_date > '{$this->today}'";
        } elseif ($status === 'draft') {
            $this->sql .= " and p.published_date is null";
        }

        if ($limit) {
            $this->sql .= ' limit ?';
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= ' offset ?';
            $this->bindValues[] = $offset;
        }

        return $this->find();
    }

    /**
     * Find All Published Page Content
     *
     * Gets all pages and collection pages without elements
     * @param  int  $limit
     * @param  int  $offset
     * @return array|null
     */
    public function findPublishedContent(int $limit = null, int $offset = null): ?array
    {
        $this->makeSelect(true);
        $this->sql .= " and p.published_date <= '{$this->today}'";

        if ($limit) {
            $this->sql .= ' limit ?';
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= ' offset ?';
            $this->bindValues[] = $offset;
        }

        return $this->find();
    }

    /**
     * Find All Pages
     *
     * Gets all pages without elements
     * Does not include collection detail pages
     * @param  string $status 'draft'|'pending'|'published'|'all'
     * @param  int  $limit
     * @param  int  $offset
     * @return array|null
     */
    public function findPages(string $status = 'published', int $limit = null, int $offset = null): ?array
    {
        $this->makeSelect(true);
        $this->sql .= ' and p.collection_id is null';

        if ($status === 'published') {
            $this->sql .= " and p.published_date <= '{$this->today}'";
        } elseif ($status === 'pending') {
            $this->sql .= " and p.published_date > '{$this->today}'";
        } elseif ($status === 'draft') {
            $this->sql .= " and p.published_date is null";
        }

        // Sort pages by 1. Draft, 2. Pending, 3. Published
        $this->sql .= ' order by p.published_date is null desc, p.published_date desc';

        if ($limit) {
            $this->sql .= ' limit ?';
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= ' offset ?';
            $this->bindValues[] = $offset;
        }

        return $this->find();
    }

    /**
     * Find Published Page By Slug
     *
     * Finds published page by by slug
     * @param string  $pageSlug       Page slug
     * @return PitonEntity|null
     */
    public function findPublishedPageBySlug(?string $pageSlug): ?PitonEntity
    {
        $this->makeSelect();
        $this->sql .= " and p.collection_id is null and p.page_slug = ? and published_date <= '{$this->today}';";
        $this->bindValues[] = $pageSlug;

        return $this->findRow();
    }

    /**
     * Find Published Collection Detail Page By Slug
     *
     * Finds published collection detail page
     * @param string  $collectionSlug Collection slug
     * @param string  $pageSlug       Page slug
     * @return PitonEntity|null
     */
    public function findPublishedCollectionPageBySlug(?string $collectionSlug, ?string $pageSlug): ?PitonEntity
    {
        $this->makeSelect();
        $this->sql .= " and c.collection_slug = ? and p.page_slug = ? and published_date <= '{$this->today}';";
        $this->bindValues[] = $collectionSlug;
        $this->bindValues[] = $pageSlug;

        return $this->findRow();
    }

    /**
     * Find All Collection Pages by Collection Slug
     *
     * Finds all related collection detail pages
     * @param  string   $collectionSlug
     * @param  string $status 'draft'|'pending'|'published'|'all'
     * @param  int  $limit
     * @param  int  $offset
     * @return array|null
     */
    public function findCollectionPagesBySlug(
        string $collectionSlug,
        string $status = 'published',
        int $limit = null,
        int $offset = null
    ): ?array {
        $this->makeSelect(true);
        $this->sql .= ' and c.collection_slug = ?';
        $this->bindValues[] = $collectionSlug;

        if ($status === 'published') {
            $this->sql .= " and p.published_date <= '{$this->today}'";
        } elseif ($status === 'pending') {
            $this->sql .= " and p.published_date > '{$this->today}'";
        } elseif ($status === 'draft') {
            $this->sql .= " and p.published_date is null";
        }

        // Sort pages by 1. Draft, 2. Pending, 3. Published
        $this->sql .= ' order by p.published_date is null desc, p.published_date desc';

        if ($limit) {
            $this->sql .= ' limit ?';
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= ' offset ?';
            $this->bindValues[] = $offset;
        }

        return $this->find();
    }

    /**
     * Find All Collection Pages by Collection ID
     *
     * Finds all related collection detail pages
     * @param  int   $collectionId
     * @param  string $status 'draft'|'pending'|'published'|'all'
     * @param  int  $limit
     * @param  int  $offset
     * @return array|null
     */
    public function findCollectionPagesById(
        int $collectionId,
        string $status = 'published',
        int $limit = null,
        int $offset = null
    ): ?array {
        $this->makeSelect(true);
        $this->sql .= ' and c.id = ?';
        $this->bindValues[] = $collectionId;

        if ($status === 'published') {
            $this->sql .= " and p.published_date <= '{$this->today}'";
        } elseif ($status === 'pending') {
            $this->sql .= " and p.published_date > '{$this->today}'";
        } elseif ($status === 'draft') {
            $this->sql .= " and p.published_date is null";
        }

        // Sort pages by 1. Draft, 2. Pending, 3. Published
        $this->sql .= ' order by p.published_date is null desc, p.published_date desc';

        if ($limit) {
            $this->sql .= ' limit ?';
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= ' offset ?';
            $this->bindValues[] = $offset;
        }

        return $this->find();
    }

    /**
     * Find All Collection Detail Pages
     *
     * Finds all pages, does not include element data
     * @param  string $status 'draft'|'pending'|'published'|'all'
     * @param  int  $limit
     * @param  int  $offset
     * @return array|null
     */
    public function findCollectionPages(string $status = 'published', int $limit = null, int $offset = null): ?array
    {
        $this->makeSelect(true);
        $this->sql .= ' and p.collection_id is not null';

        if ($status === 'published') {
            $this->sql .= " and p.published_date <= '{$this->today}'";
        } elseif ($status === 'pending') {
            $this->sql .= " and p.published_date > '{$this->today}'";
        } elseif ($status === 'draft') {
            $this->sql .= " and p.published_date is null";
        }

        // Sort pages by 1. Draft, 2. Pending, 3. Published
        $this->sql .= ' order by p.published_date is null desc, p.published_date desc';

        if ($limit) {
            $this->sql .= " limit ?";
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= " offset ?";
            $this->bindValues[] = $offset;
        }

        return $this->find();
    }

    /**
     * Page Count by Collection ID
     *
     * Returns the total number of pages by collection ID
     * @param  int $collectionId
     * @return int
     */
    public function pageCountByCollectionId(int $collectionId): ?int
    {
        $this->sql = 'select count(*) rows from page where collection_id = ?;';
        $this->bindValues[] = $collectionId;
        $this->fetchMode = PDO::FETCH_COLUMN;

        $this->execute();

        return $this->statement->fetch() ?: 0;
    }

    /**
     * Make Default Page Select
     *
     * Make select statement with outer join to media
     * Overrides and sets $this->sql.
     * @param  bool $foundRows Set to true to get foundRows() after query
     * @return void
     */
    protected function makeSelect(bool $foundRows = false)
    {
        $modifier = $foundRows ? 'SQL_CALC_FOUND_ROWS ' : '';
        $this->sql = <<<SQL
select $modifier
    c.collection_slug,
    c.collection_title,
    p.*,
    m.id media_id,
    m.filename media_filename,
    m.width media_width,
    m.height media_height,
    m.feature media_feature,
    m.caption media_caption
from page p
left outer join collection c on c.id = p.collection_id
left outer join media m on m.id = p.media_id
where 1=1
SQL;
    }
}
