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
 * Piton Navigation Mapper
 */
class NavigationMapper extends DataMapperAbstract
{
    protected $table = 'navigation';
    protected $modifiableColumns = ['navigator','parent_id','sort','page_id','title', 'url'];

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
     * @return array|null
     */
    public function findNavigation(string $navigator): ?array
    {
        $this->sql =<<<SQL
select
    n.id, n.navigator, n.parent_id, n.sort, n.page_id,
    p.title page_title, n.title nav_title, p.published_date, p.page_slug, n.url
from navigation n
left join page p on n.page_id = p.id and p.collection_id is null
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
     * @return array|null
     */
    public function findNavHierarchy(
        string $navigator,
        string $currentRoute = null,
        bool $published = true
    ): ?array {
        // Get navigator rows
        $this->allNavRows = $this->findNavigation($navigator) ?? [];

        // Recursive depth indicator
        $level =  1;

        foreach ($this->allNavRows as &$row) {
            // Skip if page is not published
            if ($published && !is_null($row->page_id) && (is_null($row->published_date) || $row->published_date > $this->today)) {
                continue;
            }

            // Assign top level nav rows (parent_id is null)
            if ($row->parent_id === null) {
                $row->level = $level;

                // Is this the current route? If so set currentPage flag
                if ($currentRoute === $row->page_slug) {
                    $row->currentPage = true;
                }

                // Set nav title, default to page title
                $row->title = $row->nav_title ?? $row->page_title;

                // Asign to navigator array
                $this->newNav[] = &$row;

                // Add any nav children
                $this->addChildNavItem($row, $level, $currentRoute, $published);
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
     * @return void
     */
    protected function addChildNavItem(&$parent, int $level, ?string $currentRoute, bool $published): void
    {
        // Recursive depth indicator
        $level++;

        // Go through all nav rows and append child of $parent
        foreach ($this->allNavRows as &$row) {
            // Skip if page is not published
            if ($published && (is_null($row->published_date) || $row->published_date > $this->today)) {
                continue;
            }

            if ($parent->id === $row->parent_id) {
                $row->level = $level;

                // Is this the current route?
                if ($currentRoute === $row->page_slug) {
                    $row->currentPage = true;
                }

                // Set nav title, default to page title
                $row->title = $row->nav_title ?? $row->page_title;

                // If parent row already has child array, then assign child to parent, othwerwise create childNav array
                isset($parent->childNav) ?: $parent->childNav = [];
                $parent->childNav[] = &$row;

                // Find any children
                $this->addChildNavItem($row, $level, $currentRoute, $published);
            }
        }
    }

    /**
     * Delete Navigation Link
     *
     * Recursively deletes children of nav link
     * @param  int  $navId
     * @return bool
     */
    public function deleteByNavId(int $navId): bool
    {
        $this->sql = 'delete from `navigation` where `id` = ?;';
        $this->bindValues[] =  $navId;

        return $this->execute();
    }

    /**
     * Delete by Page ID
     *
     * Delete navigation record by page_id
     * @param  int $pageId Page ID
     * @return bool
     */
    public function deleteByPageId(int $pageId): bool
    {
        $this->sql = 'delete from `navigation` where `page_id` = ?;';
        $this->bindValues[] =  $pageId;

        return $this->execute();
    }
}
