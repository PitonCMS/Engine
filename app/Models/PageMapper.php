<?php
/**
 * Page Mapper
 */
namespace Piton\Models;

class PageMapper extends DataMapperAbstract
{
    protected $table = 'page';
    protected $modifiableColumns = ['title', 'url', 'url_locked', 'meta_description', 'sort', 'template', 'restricted'];

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
}
