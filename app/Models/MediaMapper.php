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
        $this->makeSelect();
        $this->sql .= ' order by m.created_date desc';

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

        $this->makeSelect();
        $this->sql .= ' and mc.id = ? order by m.created_date desc';
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

        $this->makeSelect();
        $this->sql .= ' and mc.category = ? order by m.created_date desc';
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

    /**
     * Make Default Media Select
     *
     * Make select statement
     * Overrides and sets $this->sql.
     * @param  bool $foundRows Set to true to get foundRows() after query
     * @param  bool $outerJoin True = All media rows, False = only matching category rows
     * @return void
     */
    protected function makeSelect(bool $foundRows = false, $outerJoin = true): void
    {
        $foundRows = ($foundRows) ? ' SQL_CALC_FOUND_ROWS ' : '';
        $outer = ($outerJoin) ? 'left outer ' : '';
        $this->sql = <<<SQL
select $foundRows
    mc.category,
    m.id,
    m.filename,
    m.width,
    m.height,
    m.feature,
    m.caption,
    m.optimized,
    m.mime_type
from media m
$outer join media_category_map mcm on m.id = mcm.media_id
$outer join media_category mc on mc.id = mcm.category_id
where 1=1
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
        // Set a key on 'new' rows
        $this->sql = "update `media` set `optimized` = '$key' where `optimized` = ? and `mime_type` in ('image/png', 'image/jpeg');";
        $this->bindValues[] = $this->optimizedStatus['new'];
        $this->execute();

        // Now get rows marked for optimization
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
        $this->sql = "select `id` from `media` where `optimized` = ?;";
        $this->bindValues[] = $key;

        return ($this->findRow()) ?? false;
    }

    /**
     * Set Optimized Complete Status
     *
     * After optimizaion, set media row to completed
     * @param int $id Media ID
     * @return void
     */
    public function setOptimizedStatus(int $id): void
    {
        $this->sql = "update `media` set `optimized` = '{$this->optimizedStatus['complete']}' where `id` = ?";
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
