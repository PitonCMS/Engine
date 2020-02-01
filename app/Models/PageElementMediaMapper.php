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
 * Piton Page Element Media Mapper
 */
class PageElementMediaMapper extends DataMapperAbstract
{
    protected $table = 'page_element';
    protected $domainObjectClass = __NAMESPACE__ . '\Entities\PageElementMedia';

    /**
     * Find Elements by Page ID
     *
     * @param int    $pageId Page ID
     * @return array|null
     */
    public function findElementsByPageId(int $pageId): ?array
    {
        $this->sql = <<<SQL
select page_element.*, media.id media_id, media.filename media_filename, media.width media_width, media.height media_height, media.feature media_feature, media.caption media_caption
from page_element
left outer join media on media.filename = page_element.image_path
where page_element.page_id = ? order by block_key, element_sort
SQL;

        $this->bindValues[] = $pageId;

        return $this->find();
    }
}
