<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 Wolfgang Moritz
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
     * Gets all pages and/or collection pages without elements
     * @param  string $status 'draft'|'pending'|'published'|'all'
     * @param  string $type   'all'|'pages'|{collection_slug}
     * @param  int  $limit
     * @param  int  $offset
     * @return array|null
     */
    public function findContent(?string $status, ?string $type, int $limit = null, int $offset = null): ?array
    {
        $this->makeSelect();

        // Status filter. status = all skips these statement
        if ($status === 'published') {
            $this->sql .= " and p.published_date <= '{$this->today}'";
        } elseif ($status === 'pending') {
            $this->sql .= " and p.published_date > '{$this->today}'";
        } elseif ($status === 'draft') {
            $this->sql .= " and p.published_date is null";
        }

        // Type filter, type = all skips these statements
        if (!empty($type) && $type === 'pages') {
            // Only pages, no collections
            $this->sql .= " and c.collection_slug is null";
        } elseif (!empty($type) && $type !== 'all') {
            // Collection filter
            $this->sql .= " and c.collection_slug = ?";
            $this->bindValues[] = $type;
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
     * Text Search Published Content
     *
     * This query searches each of these fields for having all supplied terms:
     *  - page.title, page.sub_title page.meta_description
     *  - page_element.title, page_element.content
     * @param  string $terms                Search terms
     * @param  int    $limit                Limit
     * @param  int    $offset               Offset
     * @return array|null
     */
    public function searchPublishedContent(string $terms, int $limit = null, int $offset = null): ?array
    {
        $this->makeSelect();
        $this->sql .= " and p.published_date <= '{$this->today}'";
        $this->sql .= ' and match(p.title, p.sub_title, p.meta_description) against (? IN BOOLEAN MODE)';
        $this->bindValues[] = $terms;

        // Include page elements in search
        $this->sql .=<<<SQL
 or p.id in (
    select pes.page_id
    from page_element pes
    where match(pes.title, pes.content) against(?)
)
SQL;

        $this->bindValues[] = $terms;
        $this->sql .= ' order by p.created_date desc';

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
     * Text Search Content
     *
     * This query searches each of these fields for having all supplied terms:
     *  - page.title, page.sub_title page.meta_description
     *  - page_element.title, page_element.content
     * @param  string $terms                Search terms
     * @param  int    $limit                Limit
     * @param  int    $offset               Offset
     * @return array|null
     */
    public function searchContent(string $terms, int $limit = null, int $offset = null): ?array
    {
        $this->makeSelect();
        $this->sql .= ' and match(p.title, p.sub_title, p.meta_description) against (? IN BOOLEAN MODE)';
        $this->bindValues[] = $terms;

        // Include page elements in search
        $this->sql .=<<<SQL
 or p.id in (
    select pes.page_id
    from page_element pes
    where match(pes.title, pes.content) against(?)
)
SQL;

        $this->bindValues[] = $terms;
        $this->sql .= ' order by p.created_date desc';

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
     * Find All Published Page Content
     *
     * Gets all pages and collection pages without elements
     * @param  int  $limit
     * @param  int  $offset
     * @return array|null
     */
    public function findPublishedContent(int $limit = null, int $offset = null): ?array
    {
        $this->makeSelect();
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
     * Find Published Page By Slug
     *
     * Finds published page by by slug
     * @param string  $pageSlug       Page slug
     * @return PitonEntity|null
     */
    public function findPublishedPageBySlug(?string $pageSlug): ?PitonEntity
    {
        $this->makeSelect();
        $this->sql .= " and p.collection_id is null and p.page_slug = ? and p.published_date <= '{$this->today}';";
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
        $this->sql .= " and c.collection_slug = ? and p.page_slug = ? and p.published_date <= '{$this->today}';";
        $this->bindValues[] = $collectionSlug;
        $this->bindValues[] = $pageSlug;

        return $this->findRow();
    }

    /**
     * Find Published Collection Pages by Collection ID
     *
     * Finds all related collection detail pages for summary
     * @param  int   $collectionId
     * @param  int  $limit
     * @param  int  $offset
     * @return array|null
     */
    public function findPublishedCollectionPagesById(
        ?int $collectionId,
        int $limit = null,
        int $offset = null
    ): ?array {
        $this->makeSelect();
        $this->sql .= " and c.id = ? and p.published_date <= '{$this->today}'";
        $this->bindValues[] = $collectionId;

        $this->sql .= ' order by p.published_date desc';

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
     * Make Default Page Select
     *
     * Make select statement with outer join to media
     * Overrides and sets $this->sql.
     * @param bool $foundRows
     * @return void
     */
    protected function makeSelect(bool $foundRows = false)
    {
        $this->sql = <<<SQL
select SQL_CALC_FOUND_ROWS
    c.collection_slug,
    c.collection_title,
    p.*,
    pe.content first_element_content,
    m.id media_id,
    m.filename media_filename,
    m.width media_width,
    m.height media_height,
    m.feature media_feature,
    m.caption media_caption
from page p
left join collection c on c.id = p.collection_id
left join media m on m.id = p.media_id
left join page_element pe on p.id = pe.page_id and pe.element_sort = (select min(e.element_sort) from page_element e where e.page_id = pe.page_id)
where 1=1
SQL;
    }
}
