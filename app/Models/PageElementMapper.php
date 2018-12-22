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
        $this->sql = <<<'SQL'
select
    pe.id,
    pe.section_name,
    pe.element_type,
    pe.element_sort,
    pe.title,
    pe.content_raw,
    pe.content,
    pe.excerpt,
    pe.collection_id,
    pe.gallery_id,
    pe.image_path,
    pe.video_path
from page_element pe
where pe.page_id = ?
order by pe.section_name, pe.element_sort
SQL;

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
