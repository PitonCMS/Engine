<?php
/**
 * Page Mapper
 */
namespace Piton\Models;

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
     * Find Page
     *
     * Finds page by ID or by URL
     * @param mixed  $pageId Page ID or URL
     * @return mixed         Page object or null if not found
     */
    public function findPage($pageId)
    {
        $this->makeSelect();

        if (is_numeric($pageId)) {
            $this->sql .= ' where id = ?';
            $this->bindValues[] = (int) $pageId;
        } elseif (is_string($pageId)) {
            $this->sql .= ' where url = ?';
            $this->bindValues[] = $pageId;
        } else {
            throw new \Exception('Unknown page identifier type', 1);
        }

        return $this->findRow();
    }
}
