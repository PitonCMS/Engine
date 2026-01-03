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
 * Piton Media Category Map Value Object
 */
class MediaCategoryMap extends PitonEntity
{
    // Assigned Properties
    protected ?int $media_id = null;
    protected ?int $category_id = null;
    protected ?int $media_sort = null;

    /**
     * Constructor
     *
     * @param ?array $row Data array from query
     */
    public function __construct(?array $row)
    {
        // Load properties
        $this->media_id = isset($row['media_id']) ? $row['media_id'] : null;
        $this->category_id = isset($row['category_id']) ? $row['category_id'] : null;
        $this->media_sort = isset($row['media_sort']) ? (int) $row['media_sort'] : null;

        parent::__construct($row);
    }
}
