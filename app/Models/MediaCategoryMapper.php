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
 * Piton Media Category Mapper
 */
class MediaCategoryMapper extends DataMapperAbstract
{
    protected $table = 'media_category';
    protected $modifiableColumns = [
        'category'
    ];

    /**
     * Find Categories
     *
     * Find all categories sorted by category name
     * @param  void
     * @return mixed
     */
    public function findCategories()
    {
        $this->makeSelect();
        $this->sql .= ' order by category';

        return $this->find();
    }

    /**
     * Find Media By Category ID
     *
     * Find media by category ID
     * @param  int   $catId
     * @return mixed
     */
    public function findMediaByCategoryId(int $catId = null)
    {
        if (null === $catId) {
            return;
        }

        $this->sql = <<<SQL
select
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
        return $this->find();
    }

    /**
     * Save Media Category Assignments
     *
     * For a media ID, save category array
     * @param int   $mediaId
     * @param array $categoryIds
     * @return mixed
     */
    public function saveMediaCategoryAssignments(int $mediaId, array $categoryIds = null)
    {
        // Delete current category assignments for this media ID
        $this->deleteMediaCategoryAssignmentsByMediaId($mediaId);

        // Insert all assignments, if the category ID's array is not empty
        if (null !== $categoryIds) {
            $this->sql = 'insert into media_category_map (media_id, category_id) values ';
            foreach ($categoryIds as $catId) {
                $this->sql .= '(?, ?),';
                $this->bindValues[] = $mediaId;
                $this->bindValues[] = $catId;
            }
            $this->sql = rtrim($this->sql, ',') . ';';
            $this->execute();
        }
    }

    /**
     * Find All Media Category Assignments
     *
     * @param void
     * @return mixed
     */
    public function findAllMediaCategoryAssignments()
    {
        $this->sql = 'select media_id, category_id from media_category_map;';
        return $this->find();
    }

    /**
     *  Delete Media Category Assignments by Media ID
     *
     * @param int    $mediaId
     * @return mixed
     */
    public function deleteMediaCategoryAssignmentsByMediaId(int $mediaId)
    {
        $this->sql = 'delete from media_category_map where media_id = ?';
        $this->bindValues[] = $mediaId;
        $this->execute();
    }

    /**
     *  Delete Media Category Assignments by Category ID
     *
     * @param int    $categoryId
     * @return mixed
     */
    public function deleteMediaCategoryAssignmentsByCategoryId(int $categoryId)
    {
        $this->sql = 'delete from media_category_map where category_id = ?';
        $this->bindValues[] = $categoryId;
        $this->execute();
    }
}
