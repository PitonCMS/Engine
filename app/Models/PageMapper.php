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
        'collection_id',
        'definition',
        'template',
        'title',
        'sub_title',
        'slug',
        'meta_description',
        'published_date',
        'image_path'
    ];
    protected $domainObjectClass = __NAMESPACE__ . '\Page';

    /**
     * Find Published Page By Slug
     *
     * Finds published page by by slug
     * Does not include collections
     * @param mixed  $pageSlug Page slug
     * @return mixed           Page object or null if not found
     */
    public function findPublishedPageBySlug($pageSlug)
    {
        $this->makeSelect();

        if (is_string($pageSlug)) {
            $this->sql .= ' and collection_id is null and slug = ?';
            $this->bindValues[] = $pageSlug;
        } else {
            throw new Exception('Unknown page identifier type');
        }

        $this->sql .= " and published_date <= '{$this->today()}'";

        return $this->findRow();
    }

    /**
     * Find All Pages
     *
     * Finds all pages, does not include element data
     * Does not include collections
     * @param  bool  $unpublished Filter on published pages
     * @return mixed                     Array | null
     */
    public function findPages($unpublished = false)
    {
        $this->makeSelect();
        $this->sql .= " and collection_id is null";

        if (!$unpublished) {
            $this->sql .= " and published_date <= '{$this->today()}'";
        }

        return $this->find();
    }

    /**
     * Find All Collections
     *
     * @return mixed Array | null
     */
    public function findCollections()
    {
        $this->makeCollectionPageSelect();

        return $this->find();
    }

    /**
     * Find Collection Pages by ID
     *
     * Finds all collection pages
     * @param  int   $collectionId
     * @param  bool  $published    Filter on published collection pages
     * @return mixed               Array | null
     */
    public function findCollectionPagesById($collectionId, $published = true)
    {
        $this->makeCollectionPageSelect();
        $this->sql .= ' and c.id = ?';
        $this->bindValues[] = $collectionId;

        if ($published) {
            $this->sql .= " and p.published_date <= '{$this->today()}'";
        }

        return $this->find();
    }

    /**
     * Find Published Collection Page By Slug
     *
     * Finds collection page by collection slug and page slug
     * @param  bool  $published Filter on published collection pages
     * @return mixed            Array | null
     */
    public function findPublishedCollectionPageBySlug($collectionSlug, $pageSlug)
    {
        $this->makeCollectionPageSelect();
        $this->sql .= " and p.published_date <= '{$this->today()}'";
        $this->sql .= ' and c.slug = ? and p.slug = ?';

        $this->bindValues[] = $collectionSlug;
        $this->bindValues[] = $pageSlug;

        return $this->findRow();
    }

    /**
     * Make Collection-Page Select
     *
     * SQL statement for collection page inner join
     * @param  void
     * @return void
     */
    protected function makeCollectionPageSelect()
    {
        $this->sql = <<<SQL
select c.id collection_id,
       c.title collection_title,
       c.slug collection_slug,
       p.*
from page p
join collection c on p.collection_id = c.id
where 1=1
SQL;
    }
}
