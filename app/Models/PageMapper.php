<?php
/**
 * Piton Page Mapper
 */
namespace Piton\Models;

use Piton\ORM\DataMapperAbstract;

class PageMapper extends DataMapperAbstract
{
    protected $table = 'page';
    protected $modifiableColumns = [
        'title',
        'url',
        'url_locked',
        'layout',
        'meta_description',
        'published_date'
    ];

    /**
     * Find Published Page By URL
     *
     * Finds published-only page by by URL
     * @param mixed  $pageId Page URL
     * @return mixed         Page object or null if not found
     */
    public function findPublishedPageByUrl($pageUrl)
    {
        $this->makeSelect();

        if (is_string($pageUrl)) {
            $this->sql .= ' and url = ?';
            $this->bindValues[] = $pageUrl;
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
     * @param none
     * @return mixed Array on success
     */
    public function findPublishedPages()
    {
        $this->makeSelect();
        $this->sql .= " and published_date <= '{$this->today()}'";

        return $this->find();
    }
}
