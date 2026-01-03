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
 * Piton Media Category Value Object
 */
class MediaCategory extends PitonEntity
{
    // Assigned Properties
    protected ?string $category = null;

    /**
     * Constructor
     *
     * @param ?array $row Data array from query
     */
    public function __construct(?array $row)
    {
        // Load properties
        $this->category = isset($row['category']) ? $row['category'] : null;

        parent::__construct($row);
    }
}
