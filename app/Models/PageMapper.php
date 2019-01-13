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
     * Finds published-only page by by slug
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
            throw new Exception('Unknown page identifier type', 1);
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
     * Does not include collections
     * @param  bool  $published Filter on published collection pages
     * @return mixed            Array | null
     */
    public function findCollectionPages($published = true)
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
where published_date <= '{$this->today()}'
SQL;

        return $this->find();
    }
}
