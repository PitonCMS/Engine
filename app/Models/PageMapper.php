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
     * @param mixed  $pageSlug Page slug
     * @return mixed           Page object or null if not found
     */
    public function findPublishedPageBySlug($pageSlug)
    {
        $this->makeSelect();

        if (is_string($pageSlug)) {
            $this->sql .= ' and slug = ?';
            $this->bindValues[] = $pageSlug;
        } else {
            throw new Exception('Unknown page identifier type', 1);
        }

        $this->sql .= " and published_date <= '{$this->today()}'";

        return $this->findRow();
    }

    /**
     * Find All Published Pages
     *
     * Finds all published pages without element data
     * Does not include collections
     * @param none
     * @return mixed Array on success
     */
    public function findPublishedPages()
    {
        $this->makeSelect();
        $this->sql .= " and collection_id is null and published_date <= '{$this->today()}'";

        return $this->find();
    }
}
