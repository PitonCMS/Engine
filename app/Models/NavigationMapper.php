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
 * Piton Navigation Mapper
 */
class NavigationMapper extends DataMapperAbstract
{
    protected $table = 'navigation';
    protected $modifiableColumns = ['navigator','parent_id','sort','page_id','title','active'];

    /**
     * All Navigation Data Rows
     * @var array
     */
    protected $allNavRows;

    /**
     * New Navigation
     * @var array
     */
    protected $newNav;

    /**
     * Find Navigation
     *
     * Finds all navigation rows by navigator name
     * @param  string $navigator Name of navigator
     * @return mixed             Array|null
     */
    public function findNavigation(string $navigator)
    {
        $this->sql =<<<SQL
select n.id, n.navigator, n.parent_id, n.sort, n.page_id, p.title, n.title nav_title, n.active, p.published_date
from page p
join navigation n on p.id = n.page_id
where n.navigator = ?
order by n.sort
SQL;
        $this->bindValues[] = $navigator;

        return $this->find();
    }

    /**
     * Navigation Hierarchy
     *
     * Get navigation and build hierarchy
     * @param  string $navigator
     * @param  bool  $includeUnpublished Filter on published pages
     * @return mixed                     Array|null
     */
    public function findNavHierarchy(string $navigator, bool $includeUnpublished = false)
    {
        // Get navigator rows
        $this->allNavRows = $this->findNavigation($navigator, $includeUnpublished);

        // Set top level (parent_id is null) rows first
        foreach ($this->allNavRows as &$row) {
            // Skip if page is not published
            if ($includeUnpublished && $row->published_date <= $this->today()) {
                continue;
            }

            if ($row->parent_id === null) {
                $row->level = 1;
                $this->newNav[] = &$row;
                // Find any children
                $this->addChildNav($row, 1);
            }
        }

        return $this->newNav;
    }

    /**
     * Add Child Nav Rows
     *
     * Recursive function to build multidimensional navigation object
     * @var object  $parent
     * @var integer $level  Recursion Depth
     * @return void
     */
    protected function addChildNav(&$parent, $level)
    {
        // Recursive depth indicator
        $level++;

        // Go through raw nav rows and append child of $parent
        foreach ($this->allNavRows as &$row) {
            if ($parent->id === $row->parent_id) {
                $row->level = $level;
                isset($parent->childNav) ?: $parent->childNav = [];
                $parent->childNav[] = &$row;
                // Find any children
                $this->addChildNav($row, $level);
            }
        }
    }

    /**
     * Delete by Page ID
     *
     * Delete navigation record by page_id
     * @param  int $pageId Page ID
     * @return void
     */
    public function deleteByPageId(int $pageId)
    {
        // TODO Remove child rows if parent is being deleted
        $this->sql = "delete from `{$this->table}` where `page_id` = ?";
        $this->bindValues[] = $pageId;

        return $this->execute();
    }
}
