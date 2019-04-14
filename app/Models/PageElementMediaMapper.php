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
 * Piton Page Element Media Mapper
 */
class PageElementMediaMapper extends DataMapperAbstract
{
    protected $table = 'page_element';
    protected $tableJoins = [
        [
            'select' => 'media.file media_file, media.caption media_caption',
            'table' => 'media',
            'join' => 'left outer join',
            'on' => 'media.file = page_element.image_path'
        ]
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
        $this->sql .= ' and page_element.page_id = ? order by block_key, element_sort';
        $this->bindValues[] = $pageId;

        return $this->find();
    }
}
