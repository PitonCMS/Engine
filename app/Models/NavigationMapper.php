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

    protected $allNavRows;
    protected $newNav;

    /**
     * Find Navigation
     *
     * Finds all pages and navigation rows
     * @param  string $navigator
     * @return mixed             Array|null
     */
    public function findNavigation(string $navigator)
    {
        $this->sql =<<<'SQL'
select n.id, n.navigator, n.parent_id, n.sort, n.page_id, p.title, n.title nav_title, n.active, p.published_date
from page p
join navigation n on p.id = n.page_id
where n.navigator = ?
-- and n.active = 'Y'
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
     * @return mixed             Array|null
     */
    public function findNavHierarchy(string $navigator, bool $includeUnpublished = false)
    {
        // Get navigator rows
        $this->allNavRows = $this->findNavigation($navigator);

        // Set top level (parent_id is null) rows first
        foreach ($this->allNavRows as &$row) {
            if ($row->parent_id === null) {
                $row->level = 1;
                $this->newNav[] = &$row;
                // Find any children
                $this->addChildNav($row, 1);
            }
        }

        return $this->newNav;
    }

    protected function addChildNav(&$parent, $level)
    {
        $level++;

        // Go through raw nav rows
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
        // TODO Delete child rows if parent
        $this->sql = "delete from {$this->table} where `page_id` = ?";
        $this->bindValues[] = $pageId;

        return $this->execute();
    }

    /**
     *
     */
    public function deleteNavParentAndChildren(int $navId)
    {
        $this->sql = "delete from {$this->table} where `id` = ?";
        $this->bindValues[] = $navId;

        return $this->execute();
    }
}
