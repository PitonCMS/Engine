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
        'title',
        'sub_title',
        'slug',
        'template',
        'meta_description',
        'published_date'
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
     * @param  bool  $published Filter on published pages
     * @return mixed            Array | null
     */
    public function findPages($published = true)
    {
        $this->makeSelect();
        $this->sql .= " and collection_id is null";

        if ($published) {
            $this->sql .= " and published_date <= '{$this->today()}'";
        }

        return $this->find();
    }

    /**
     * Find All Collection Pages
     *
     * Finds all collection pages, does not include element data
     * @param  bool  $published Filter on published collection pages
     * @return mixed            Array | null
     */
    public function findCollectionPages($published = true)
    {
        $this->makeCollectionPageSelect();

        if ($published) {
            $this->sql .= " and p.published_date <= '{$this->today()}'";
        }

        return $this->find();
    }

    /**
     * Find Published Collection Page Detail By Slug
     *
     * Finds collection page, does not include element data
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
       c.kind collection_kind,
       p.id,
       p.title,
       p.sub_title,
       p.slug,
       p.template,
       p.meta_description,
       p.published_date
from page p
join collection c on p.collection_id = c.id
where 1=1
SQL;
    }
}
