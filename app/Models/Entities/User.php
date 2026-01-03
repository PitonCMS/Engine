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
 * Piton User Value Object
 */
class User extends PitonEntity
{
    // Assigned Properties
    protected ?string $first_name = null;
    protected ?string $last_name = null;
    protected ?string $email = null;
    protected ?string $role = null;
    protected ?string $active = null;

    /**
     * Constructor
     *
     * @param ?array $row Data array from query
     */
    public function __construct(?array $row)
    {
        // Load properties
        $this->first_name = isset($row['first_name']) ? $row['first_name'] : null;
        $this->last_name = isset($row['last_name']) ? $row['last_name'] : null;
        $this->email = isset($row['email']) ? $row['email'] : null;
        $this->role = isset($row['role']) ? $row['role'] : null;
        $this->active = isset($row['active']) ? $row['active'] : null;

        parent::__construct($row);
    }
}
