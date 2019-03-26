<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Models;

use Piton\ORM\DataMapperAbstract;

/**
 * Piton Page Element Mapper
 */
class PageElementMapper extends DataMapperAbstract
{
    protected $table = 'page_element';
    protected $modifiableColumns = [
        'page_id',
        'block_key',
        'template',
        'definition',
        'element_sort',
        'title',
        'content_raw',
        'content',
        'excerpt',
        'collection_id',
        'gallery_id',
        'image_path',
        'embedded'
    ];

    /**
     * Find Elements by Page ID
     *
     * @param int    $pageId Page ID
     * @return mixed Array or null
     */
    public function findElementsByPageId($pageId)
    {
        $this->makeElementMediaSelect();
        $this->sql .= ' and pe.page_id = ? order by block_key, element_sort';
        $this->bindValues[] = $pageId;

        return $this->find();
    }

    /**
     * Delete Page Elements by Page ID
     *
     * @param int   $pageId Page ID
     * @return void
     */
    public function deleteElementsByPageId($pageId)
    {
        $this->sql = "delete from {$this->table} where page_id = ?;";
        $this->bindValues[] = $pageId;

        return $this->execute();
    }

    /**
     * Get With Media Meta Data
     *
     * Left outer joins to media table on image file name
     * @param void
     * @return void
     */
    protected function makeElementMediaSelect()
    {
        $this->sql = <<<SQL
select m.id media_id,
       m.file media_file,
       m.caption media_caption,
       pe.*
from page_element pe
left outer join media m on pe.image_path = m.file
where 1=1
SQL;
    }
}
