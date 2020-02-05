<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

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
        'collection_slug',
        'gallery_id',
        'image_path',
        'embedded'
    ];

    /**
     * Find Elements by Page ID
     *
     * @param int    $pageId Page ID
     * @return array|null
     */
    public function findElementsByPageId(int $pageId): ?array
    {
        $this->makeSelect();
        $this->sql .= ' and page_element.page_id = ? order by block_key, element_sort';
        $this->bindValues[] = $pageId;

        return $this->find();
    }

    /**
     * Delete Page Elements by Page ID
     *
     * @param int   $pageId Page ID
     * @return bool
     */
    public function deleteElementsByPageId($pageId): bool
    {
        $this->sql = "delete from {$this->table} where page_id = ?;";
        $this->bindValues[] = $pageId;

        return $this->execute();
    }
}
