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
 * Piton Media Mapper
 */
class MediaMapper extends DataMapperAbstract
{
    protected $table = 'media';
    protected $modifiableColumns = [
        'filename',
        'width',
        'height',
        'feature',
        'caption',
    ];
    protected $domainObjectClass = __NAMESPACE__ . '\Entities\Media';

    /**
     * Find All Media
     *
     * Return all media records
     * @param  int  $limit
     * @param  int  $offset
     * @return array|null
     */
    public function findAllMedia(int $limit = null, int $offset = null): ?array
    {
        $this->makeSelect();
        $this->sql .= ' order by created_date desc';

        if ($limit) {
            $this->sql .= " limit ?";
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= " offset ?";
            $this->bindValues[] = $offset;
        }

        return $this->find();
    }

    /**
     * Find Media By Category ID
     *
     * Find media by category ID
     * @param  int   $catId
     * @param  int  $limit
     * @param  int  $offset
     * @return array|null
     */
    public function findMediaByCategoryId(int $catId = null, int $limit = null, int $offset = null): ?array
    {
        if (null === $catId) {
            return null;
        }

        $this->sql = <<<SQL
select SQL_CALC_FOUND_ROWS
    mc.category,
    m.id,
    m.filename,
    m.width,
    m.height,
    m.feature,
    m.caption
from media_category mc
join media_category_map mcp on mc.id = mcp.category_id
join media m on mcp.media_id = m.id
where mc.id = ?
SQL;
        $this->bindValues[] = $catId;

        if ($limit) {
            $this->sql .= " limit ?";
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= " offset ?";
            $this->bindValues[] = $offset;
        }

        return $this->find();
    }

    /**
     * Find Media By Category Name (Optional)
     *
     * Find media by optional category name.
     * If no category is provided then return all
     * @param  string $category
     * @param  int  $limit
     * @param  int  $offset
     * @return array|null
     */
    public function findMediaByCategoryName(string $category = null, int $limit = null, int $offset = null): ?array
    {
        if (null === $category) {
            return $this->findAllMedia($limit, $offset);
        }

        $this->sql = <<<SQL
select SQL_CALC_FOUND_ROWS
    mc.category,
    m.id,
    m.filename,
    m.width,
    m.height,
    m.feature,
    m.caption
from media_category mc
join media_category_map mcp on mc.id = mcp.category_id
join media m on mcp.media_id = m.id
where mc.category = ?
SQL;
        $this->bindValues[] = $category;

        if ($limit) {
            $this->sql .= " limit ?";
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= " offset ?";
            $this->bindValues[] = $offset;
        }

        return $this->find();
    }
}
