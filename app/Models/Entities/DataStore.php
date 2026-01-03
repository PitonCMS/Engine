<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2026 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Models\Entities;

/**
 * Piton Data Store Value Object
 */
class DataStore extends PitonEntity
{
    // Assigned Properties
    protected ?string $category = null;
    protected ?int $page_id = null;
    protected ?int $element_id = null;
    protected ?string $setting_key = null;
    protected ?string $setting_value = null;

    /**
     * Constructor
     *
     * @param ?array $row Data array from query
     */
    public function __construct(?array $row)
    {
        // Load properties
        $this->category = isset($row['category']) ? $row['category'] : null;
        $this->page_id = isset($row['page_id']) ? (int) $row['page_id'] : null;
        $this->element_id = isset($row['element_id']) ? (int) $row['element_id'] : null;
        $this->setting_key = isset($row['setting_key']) ? $row['setting_key'] : null;
        $this->setting_value = isset($row['setting_value']) ? $row['setting_value'] : null;

        parent::__construct($row);
    }
}
