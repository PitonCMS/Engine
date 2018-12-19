<?php
/**
 * Page Element Mapper
 */
namespace Piton\Models;

class PageElementMapper extends DataMapperAbstract
{
    protected $table = 'page_element';
    protected $modifiableColumns = [
        'element_type',
        'title',
        'content_raw',
        'content',
        'collection_id',
        'media_id',
        'media_path'
    ];

    /**
     * Find Elements With Sections
     *
     * Gets all elements with current section assignments
     * @param void
     * @return mixed array|null
     */
    public function findAllElementsWithOptionalPageSections()
    {
        $this->sql = <<<'SQL'
select pe.id, pe.element_type, pe.title, pe.content, pe.media_path,
ifnull(group_concat(concat(p.title, ' > ', pse.section_name) separator '<br>'), 'Orphaned') page_references
from page_element pe
left outer join page_section_element_map pse on pe.id = pse.element_id
left outer join page p on p.id = pse.page_id
group by pe.id, pe.element_type, pe.title, pe.content
order by pe.id
SQL;

        return $this->find();
    }
}
