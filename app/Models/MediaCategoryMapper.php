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
 * Piton Media Category Mapper
 */
class MediaCategoryMapper extends DataMapperAbstract
{
    protected $table = 'media_category';
    protected $modifiableColumns = ['category'];

    /**
     * Find Categories
     *
     * Find all categories sorted by category name
     * @param  void
     * @return array|null
     */
    public function findCategories(): ?array
    {
        $this->makeSelect();
        $this->sql .= ' order by category';

        return $this->find();
    }

    /**
     * Save Media Category Assignments
     *
     * For a media ID, save category array
     * @param int        $mediaId
     * @param array|null $categoryIds
     * @return void
     */
    public function saveMediaCategoryAssignments(int $mediaId, ?array $categoryIds): void
    {
        // Delete current category assignments for this media ID
        $this->deleteMediaCategoryAssignmentsByMediaId($mediaId);

        // Insert all assignments, if the category ID's array is not empty
        if ($categoryIds) {
            $this->sql = 'insert into media_category_map (media_id, category_id) values ';
            foreach ($categoryIds as $catId) {
                $this->sql .= '(?, ?),';
                $this->bindValues[] = $mediaId;
                $this->bindValues[] = $catId;
            }
            // Remove trailing comma from last set of values
            $this->sql = rtrim($this->sql, ',') . ';';

            $this->execute();
        }
    }

    /**
     *  Delete Media Category Assignments by Media ID
     *
     * @param int    $mediaId
     * @return bool
     */
    public function deleteMediaCategoryAssignmentsByMediaId(int $mediaId): bool
    {
        $this->sql = 'delete from media_category_map where media_id = ?';
        $this->bindValues[] = $mediaId;

        return $this->execute();
    }

    /**
     *  Delete Media Category Assignments by Category ID
     *
     * @param int    $categoryId
     * @return bool
     */
    public function deleteMediaCategoryAssignmentsByCategoryId(int $categoryId): bool
    {
        $this->sql = 'delete from media_category_map where category_id = ?';
        $this->bindValues[] = $categoryId;

        return $this->execute();
    }
}
