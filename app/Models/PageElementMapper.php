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

        $pageElements = $this->find();

        // Index array
        if ($pageElements) {
            $pageElements = $this->indexPageElementKeys($pageElements);
        }

        return $pageElements;
    }

    /**
     * Index Page Element Keys
     *
     * Accepts array of page element objects, and reindexes array using the page element name as key
     * @param array of Piton\Models\DomainObject
     * @return array
     */
    protected function indexPageElementKeys(array $elements)
    {
        $newArray = [];

        foreach ($elements as $row) {
            $newArray[$row->name] = $row;
        }

        return $newArray;
    }
}
