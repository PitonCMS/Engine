<?php
/**
 * Page Element Mapper
 */
namespace Piton\Models;

class PageElementMapper extends DataMapperAbstract
{
    protected $table = 'page_element';
    protected $modifiableColumns = ['page_id', 'name', 'content_raw', 'content'];

    /**
     * Get Pages Elements by Page ID
     *
     * Returns associative array of domain objects, indexed by page_element.name
     * @param int Page ID
     * @return mixed
     */
    public function findPageElementsByPageId($id)
    {
        // Get page headers
        $this->makeSelect();
        $this->sql .= ' where page_id = ?';
        $this->bindValues[] = $id;

        return $this->find();
    }
}
