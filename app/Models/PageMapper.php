<?php
/**
 * Page Mapper
 */
namespace Piton\Models;

class PageMapper extends DataMapperAbstract
{
    protected $table = 'page';
    protected $modifiableColumns = [
        'name',
        'title',
        'url',
        'url_locked',
        'meta_description',
        'sort',
        'layout',
        'restricted'
    ];

    /**
     * Get Single Pages by URL
     *
     * Returns domain object
     * @param string, /URL
     * @return mixed
     */
    public function findPageByUrl($url)
    {
        // Get page headers
        $this->makeSelect();
        $this->sql .= ' where url = ?';
        $this->bindValues[] = $url;

        return $this->findRow();
    }

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
    p.name,
    p.title,
    p.url,
    p.meta_description,
    p.layout,
    p.restricted,
    ps.id section_id,
    ps.name section_name,
    ps.title section_title,
    pe.id element_id,
    pe.name element_name,
    pe.title element_title,
    pe.content_raw,
    pe.content
from page p
left outer join page_section ps on p.id = ps.page_id
left outer join page_element pe on pe.page_section_id = ps.id
where
SQL;

        if (is_integer($identifier)) {
            $this->sql .= ' p.id = ?';
            $this->bindValues[] = (int) $identifier;
        } else {
            $this->sql .= ' p.url = ?';
            $this->bindValues[] = $identifier;
        }

        $this->sql .= ' order by ps.sort, pe.sort';
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
        if (!isset($data)) {
            return $data;
        }

        // First assign page level properties from first array element
        $page = [];
        $page['id'] = $data[0]['id'];
        $page['name'] = $data[0]['name'];
        $page['url'] = $data[0]['url'];
        $page['meta_description'] = $data[0]['meta_description'];
        $page['layout'] = $data[0]['layout'];
        $page['restricted'] = $data[0]['restricted'];

        // Loop through remainder of data to assign sections and elements
        foreach ($data as $row) {
            // Section data
            $page['sections'][$row['section_name']]['id'] = $row['section_id'];
            $page['sections'][$row['section_name']]['name'] = $row['section_name'];
            $page['sections'][$row['section_name']]['title'] = $row['section_title'];

            // Element data
            $page['sections'][$row['section_name']]['elements'][$row['element_name']]['id'] = $row['element_id'];
            $page['sections'][$row['section_name']]['elements'][$row['element_name']]['name'] = $row['element_name'];
            $page['sections'][$row['section_name']]['elements'][$row['element_name']]['title'] = $row['element_title'];
            $page['sections'][$row['section_name']]['elements'][$row['element_name']]['content_raw'] = $row['content_raw'];
            $page['sections'][$row['section_name']]['elements'][$row['element_name']]['content'] = $row['content'];
        }

        return $page;
    }
}
