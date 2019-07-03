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
select
    n.id, n.navigator, n.parent_id, n.sort, n.page_id,
    p.title page_title, n.title nav_title, n.active, p.published_date, p.page_slug
from navigation n
left join page p on n.page_id = p.id and p.collection_slug is null
where n.navigator = ?
order by n.sort
SQL;
        $this->bindValues[] = $navigator;

        return $this->find();
    }

    /**
     * Navigation Hierarchy
     *
     * Get navigation records and build multidimensional hierarchy
     * @param  string $navigator    Navigator name
     * @param  string $currentRoute Current route path to match and set active flag
     * @param  bool   $published    Filter on published pages
     * @param  bool   $active       Filter on active links
     * @return mixed                Array|null
     */
    public function findNavHierarchy(string $navigator, string $currentRoute = null, bool $published = true, bool $active = true)
    {
        // Get navigator rows
        $this->allNavRows = $this->findNavigation($navigator);

        // Recursive depth indicator
        $level =  1;

        // Set top level (parent_id is null) rows first
        foreach ($this->allNavRows as &$row) {
            // Skip if page is not published, or navigation link not active
            if (
                ($published && $row->published_date > $this->today) ||
                ($active && $row->active === 'N')
                ) {
                continue;
            }

            // Assign top level nav rows (parent_id is null)
            if ($row->parent_id === null) {
                $row->level = $level;

                // Is this the current route? If so set currentPage flag
                if ($currentRoute === $row->page_slug) {
                    $row->currentPage = true;
                }

                // Asign to navigator array
                $this->newNav[] = &$row;

                // Add any nav children
                $this->addChildNavItem($row, $level, $currentRoute, $published, $active);
            }
        }

        return $this->newNav;
    }

    /**
     * Add Child Navigation Item
     *
     * Recursive function to build multidimensional navigation object
     * @param object  $parent       Parent nav item reference
     * @param integer $level        Recursion depth of parent
     * @param  string $currentRoute Current route path to match and set active flag
     * @param  bool   $published    Filter on published pages
     * @param  bool   $active       Filter on active links
     * @return void
     */
    protected function addChildNavItem(&$parent, int $level, ?string $currentRoute, bool $published, bool $active)
    {
        // Recursive depth indicator
        $level++;

        // Go through all nav rows and append child of $parent
        foreach ($this->allNavRows as &$row) {
            // Skip if page is not published, or navigation link not active
            if (
                ($published && $row->published_date > $this->today) ||
                ($active && $row->active === 'N')
                ) {
                continue;
            }

            if ($parent->id === $row->parent_id) {
                $row->level = $level;

                // Is this the current route?
                if ($currentRoute === $row->page_slug) {
                    $row->currentPage = true;
                }

                // If parent has child, then assign child to parent
                isset($parent->childNav) ?: $parent->childNav = [];
                $parent->childNav[] = &$row;

                // Find any children
                $this->addChildNavItem($row, $level, $currentRoute, $published, $active);
            }
        }
    }

    /**
     * Delete Navigation Link
     *
     * Recursively deletes children of nav link
     * @param  int  $navId
     * @return void
     */
    public function deleteByNavId(int $navId)
    {
        // Find any children to delete
        $this->makeSelect();
        $this->sql .= ' and `parent_id` = ?';
        $this->bindValues[] = $navId;
        $children = $this->find();

        if ($children) {
            foreach ($children as $child) {
                $this->deleteByNavId($child->id);
            }
        }

        // Delete nav link
        $this->sql = 'delete from `navigation` where `id` = ?;';
        $this->bindValues[] =  $navId;

        return $this->execute();
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
        // Get nav ID, and call deleteByNavId() to recursively delete children
        $this->makeSelect();
        $this->sql .= ' and `page_id` = ?';
        $this->bindValues[] = $pageId;
        $nav = $this->findRow();

        if ($nav) {
            $this->deleteByNavId($nav->id);
        }
    }
}
