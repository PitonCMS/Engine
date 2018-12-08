<?php
/**
 * Page Section Element Mapper
 */
namespace Piton\Models;

class PageSectionElementMapper extends DataMapperAbstract
{
    protected $table = 'page_section_element_map';
    protected $modifiableColumns = [
        'page_id',
        'section_name',
        'element_id',
        'element_sort'
    ];

    /**
     * Find Elements by Page ID
     *
     * @param int Page ID
     * @return mixed|array
     */
    public function getSectionElementsByPageId($pageId)
    {
        $this->sql = <<<'SQL'
select
    pse.id section_element_id,
    pse.section_name,
    pe.id element_id,
    pse.element_sort,
    pe.element_type,
    pe.title,
    pe.content_raw,
    pe.content,
    pe.collection_id,
    pe.media_id,
    pe.media_path
from page_section_element_map pse
join page_element pe on pse.element_id = pe.id
where pse.page_id = ?
order by pse.section_name, pse.element_sort
SQL;

        $this->bindValues[] = $pageId;

        return $this->find();
    }

    /**
     * Delete Page Section Element Mapping
     *
     * Does not delete page elments, just the mapping to a page
     * @param int Page ID
     * @return void
     */
    public function deleteSectionElementsByPageId($pageId)
    {
        $this->sql = "delete from {$this->table} where page_id = ?;";
        $this->bindValues[] = $pageId;

        $this->execute();
        $this->clear();

        return;
    }
}
