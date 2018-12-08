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
     * Get Page Data
     *
     * Gets all page data in structured array
     * @param str|int URL or Page ID
     * @return array
     */
    public function findPageData($identifier)
    {
        $this->sql = <<<'SQL'
select
    p.id,
    p.title,
    p.url,
    p.url_locked,
    p.meta_description,
    p.layout,
    p.published_date,
    pse.section_name,
    pse.element_sort,
    pe.id element_id,
    pe.element_type,
    pe.title element_title,
    pe.content_raw,
    pe.content,
    pe.collection_id,
    pe.media_id,
    pe.media_path
from page p
left outer join page_section_element_map pse on p.id = pse.page_id
left outer join page_element pe on pe.id = pse.element_id
where
SQL;

        if (is_integer($identifier)) {
            $this->sql .= ' p.id = ?';
            $this->bindValues[] = (int) $identifier;
        } else {
            $this->sql .= ' p.url = ?';
            $this->bindValues[] = $identifier;
        }

        $this->sql .= ' order by pse.section_name, pse.element_sort';
        $this->fetchMode = \PDO::FETCH_ASSOC;
        $data = $this->find();

        if ($data) {
            return $this->processPageData($data);
        }

        return null;
    }

    /**
     * Process Page Data
     *
     * Accepts one dimension array from query and creates multi-dimensional array
     * @param array
     * @return mixed
     */
    protected function processPageData($data)
    {
        // Do nothing if there is no data to process
        if (!isset($data[0]['id'])) {
            return $data;
        }

        // First assign page level properties from first array element
        $page = [];
        $page['id'] = $data[0]['id'];
        $page['title'] = $data[0]['title'];
        $page['url'] = $data[0]['url'];
        $page['url_locked'] = $data[0]['url_locked'];
        $page['meta_description'] = $data[0]['meta_description'];
        $page['layout'] = $data[0]['layout'];
        $page['published_date'] = $data[0]['published_date'];

        // Loop through remainder of section element data to assign sections and elements
        foreach ($data as $row) {
            $page['sections'][$row['section_name']][$row['element_id']]['element_id'] = $row['element_id'];
            $page['sections'][$row['section_name']][$row['element_id']]['element_type'] = $row['element_type'];
            $page['sections'][$row['section_name']][$row['element_id']]['element_title'] = $row['element_title'];
            $page['sections'][$row['section_name']][$row['element_id']]['content_raw'] = $row['content_raw'];
            $page['sections'][$row['section_name']][$row['element_id']]['content'] = $row['content'];
            $page['sections'][$row['section_name']][$row['element_id']]['collection_id'] = $row['collection_id'];
            $page['sections'][$row['section_name']][$row['element_id']]['media_id'] = $row['media_id'];
            $page['sections'][$row['section_name']][$row['element_id']]['media_path'] = $row['media_path'];
        }

        return $page;
    }
}
