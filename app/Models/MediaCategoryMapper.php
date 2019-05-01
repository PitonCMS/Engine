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
     * Find in Char Order
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
        $this->deleteMediaCategoryAssignments($mediaId);

        // Insert all assignments, if the category ID's array is not empty
        if (null !== $categoryIds) {
            $this->sql = 'insert into media_category_map (media_id, category_id) values ';
            foreach ($categoryIds as $id) {
                $this->sql .= '(?, ?),';
                $this->bindValues[] = $mediaId;
                $this->bindValues[] = $id;
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
        $this->sql = 'select media_id, category_id from media_category_map order by media_id;';
        return $this->find();
    }

    /**
     *  Delete Media Category Assignments
     *
     * @param int    $mediaId
     * @return mixed
     */
    public function deleteMediaCategoryAssignments(int $mediaId)
    {
        $this->sql = 'delete from media_category_map where media_id = ?';
        $this->bindValues[] = $mediaId;
        $this->execute();
    }
}
