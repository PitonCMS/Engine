<?php
/**
 * Page Section Mapper
 */
namespace Piton\Models;

class PageSectionMapper extends DataMapperAbstract
{
    protected $table = 'page_section';
    protected $modifiableColumns = ['page_id', 'name', 'title'];

    /**
     * Get Pages Sections by Page ID
     *
     * @param int Page ID
     * @return mixed
     */
    public function findPageSectionsByPageId($id)
    {
        // Get page headers
        $this->makeSelect();
        $this->sql .= ' where page_id = ?';
        $this->bindValues[] = $id;

        return $this->find();
    }
}
