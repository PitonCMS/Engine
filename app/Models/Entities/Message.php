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
 * Piton Message Value Object
 */
class Message extends PitonEntity
{
    // Assigned Properties
    protected ?string $name = null;
    protected ?string $email = null;
    protected ?string $message = null;
    protected ?string $is_read = null;
    protected ?string $context = null;

    /**
     * Constructor
     *
     * @param ?array $row Data array from query
     */
    public function __construct(?array $row)
    {
        // Load properties
        $this->name = isset($row['name']) ? $row['name'] : null;
        $this->email = isset($row['email']) ? $row['email'] : null;
        $this->message = isset($row['message']) ? $row['message'] : null;
        $this->is_read = isset($row['is_read']) ? $row['is_read'] : null;
        $this->context = isset($row['context']) ? $row['context'] : null;

        parent::__construct($row);
    }
}
