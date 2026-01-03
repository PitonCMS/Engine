<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2026 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Models\Entities;

/**
 * Piton Navigation Value Object
 */
class Navigation extends PitonEntity
{
    // Assigned Properties
    protected ?string $navigator = null;
    protected ?int $parent_id = null;
    protected ?int $sort = null;
    protected ?string $title = null;
    protected ?string $nav_title = null;
    protected ?string $url = null;
    protected ?int $collection_id = null;
    protected ?string $collection_title = null;
    protected ?string $collection_slug = null;
    protected ?int $page_id = null;
    protected ?string $published_date = null;
    protected ?string $page_slug = null;

    /**
     * Constructor
     *
     * @param ?array $row Data array from query
     */
    public function __construct(?array $row)
    {
        // Load properties
        $this->navigator = isset($row['navigator']) ? $row['navigator'] : null;
        $this->parent_id = isset($row['parent_id']) ? (int) $row['parent_id'] : null;
        $this->sort = isset($row['sort']) ? (int) $row['sort'] : null;
        $this->title = isset($row['title']) ? $row['title'] : null;
        $this->nav_title = isset($row['nav_title']) ? $row['nav_title'] : null;
        $this->url = isset($row['url']) ? $row['url'] : null;
        $this->collection_id = isset($row['collection_id']) ? (int) $row['collection_id'] : null;
        $this->collection_title = isset($row['collection_title']) ? $row['collection_title'] : null;
        $this->collection_slug = isset($row['collection_slug']) ? $row['collection_slug'] : null;
        $this->page_id = isset($row['page_id']) ? (int) $row['page_id'] : null;
        $this->published_date = isset($row['published_date']) ? $row['published_date'] : null;
        $this->page_slug = isset($row['page_slug']) ? $row['page_slug'] : null;

        parent::__construct($row);
    }
}
