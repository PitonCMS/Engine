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
        'mime_type',
        'optimized',
    ];
    protected $domainObjectClass = __NAMESPACE__ . '\Entities\Media';
    protected $optimizedStatus = [
        'new' => 'new',
        'complete' => 'complete',
        'retry' => 'retry',
        'exclude' => 'exclude'
    ];

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
        $this->mediaSelectJoinCategory();

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
     * @param  int|null  $categoryId
     * @param  int       $limit
     * @param  int       $offset
     * @return array|null
     */
    public function findMediaByCategoryId(?int $categoryId, int $limit = null, int $offset = null): ?array
    {
        if (null === $categoryId) {
            return null;
        }

        $this->mediaSelectJoinCategory(false, $categoryId);

        if ($limit) {
            $this->sql .= " limit ?";
            $this->bindValues[] = $limit;
        }

        if ($offset) {
            $this->sql .= " offset ?";
            $this->bindValues[] = $offset;
        }

        // return $this->find();
        return null;
    }

    /**
     * Make Default Media Select
     *
     * Make select statement
     * Overrides and sets $this->sql.
     * @param  bool $foundRows  Set to true to get foundRows() after query
     * @param  int  $categoryId Optional cateogry ID
     * @return void
     */
    protected function mediaSelectJoinCategory(bool $foundRows = false, int $categoryId = null): void
    {
        // Add where clause on category ID if one was provided
        $where = '';
        if ($categoryId) {
            $where = ' where mc.id = ?';
            $this->bindValues[] = $categoryId;
        }

        $foundRows = ($foundRows) ? ' SQL_CALC_FOUND_ROWS ' : '';

        $this->sql = <<<SQL
select $foundRows
    m.id,
    m.filename,
    m.width,
    m.height,
    m.feature,
    m.caption,
    m.optimized,
    m.mime_type,
    m.created_date,
    group_concat(mc.id) category_id_list
from media m
left join media_category_map mcm on m.id = mcm.media_id
left join media_category mc on mc.id = mcm.category_id
$where
group by
    m.id,
    m.filename,
    m.width,
    m.height,
    m.feature,
    m.caption,
    m.optimized,
    m.mime_type,
    m.created_date
order by m.created_date desc
SQL;
    }

    /**
     * Get New Media to Optimize
     *
     * Get unoptimized 'new' media files to process
     * @param  string $key
     * @return array|null
     */
    public function findNewMediaToOptimize(string $key): ?array
    {
        // Set the key on 'new' rows
        $this->sql = "update `media` set `optimized` = '$key' where `optimized` = ? and `mime_type` in ('image/png', 'image/jpeg');";
        $this->bindValues[] = $this->optimizedStatus['new'];
        $this->execute();

        // Now select those rows marked for optimization
        $this->sql = "select `id`, `filename`, `optimized` from `media` where `optimized` = ?;";
        $this->bindValues[] = $key;

        return $this->find();
    }

    /**
     * Optimized Key Exists
     *
     * Checks if the provided key is already in use - highly unlikely
     * @param string $key Key to search for
     * @return bool
     */
    public function optimizeKeyExists(string $key): bool
    {
        $this->sql = "select `id` from `media` where `optimized` = ? limit 1;";
        $this->bindValues[] = $key;

        return ($this->findRow()) ?? false;
    }

    /**
     * Set Optimized Complete Status
     *
     * After optimizaion, set media row to completed
     * @param int $id Media ID
     * @param string $status Status code from $this->optimizedStatus
     * @return void
     */
    public function setOptimizedStatus(int $id, string $status): void
    {
        $this->sql = "update `media` set `optimized` = ? where `id` = ?";
        $this->bindValues[] = $this->optimizedStatus[$status];
        $this->bindValues[] = $id;

        $this->execute();
    }

    /**
     * Get Optimized Status Code
     *
     * Returns status code for use in record
     * @param string $key
     * @return string
     */
    public function getOptimizedCode(string $key): string
    {
        return $this->optimizedStatus[$key];
    }
}
