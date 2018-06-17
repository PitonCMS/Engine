<?php
/**
 * Page Element Mapper
 */
namespace Piton\Models;

class PageElementMapper extends DataMapperAbstract
{
    protected $table = 'page_element';
    protected $modifiableColumns = ['page_section_id', 'name', 'title', 'content_raw', 'content'];

    /**
     * Get Pages Elements by Page Section ID
     *
     * @param int Page ID
     * @return mixed
     */
    public function findPageElementsByPageSectionId($id)
    {
        // Get page headers
        $this->makeSelect();
        $this->sql .= ' where page_section_id = ?';
        $this->bindValues[] = $id;

        return $this->find();
    }
}
