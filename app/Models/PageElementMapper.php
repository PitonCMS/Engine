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
        $this->sql .= ' and page_id = ? order by block_key, element_sort';
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
}
