<?php
/**
 * Page Element Mapper
 */
namespace Piton\Models;

class PageElementMapper extends DataMapperAbstract
{
    protected $table = 'page_element';
    protected $modifiableColumns = [
        'page_id',
        'section_name',
        'element_type',
        'element_sort',
        'title',
        'content_raw',
        'content',
        'excerpt',
        'collection_id',
        'gallery_id',
        'image_path',
        'video_path'
    ];

    /**
     * Find Elements by Page ID
     *
     * @param int    $pageId Page ID
     * @return mixed Array or null
     */
    public function findElementsByPageId($pageId)
    {
        $this->makeSelect();
        $this->sql .= ' where page_id = ? order by section_name, element_sort';
        $this->bindValues[] = $pageId;

        return $this->find();
    }

    /**
     * Delete Page Elements by Page ID
     *
     * @param int   $pageId Page ID
     * @return void
     */
    public function deletePageElementsByPageId($pageId)
    {
        $this->sql = "delete from {$this->table} where page_id = ?;";
        $this->bindValues[] = $pageId;

        $this->execute();
        $this->clear();

        return;
    }
}
