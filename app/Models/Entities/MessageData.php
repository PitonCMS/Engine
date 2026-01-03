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
 * Piton Message Data Value Object
 */
class MessageData extends PitonEntity
{
    // Assigned Properties
    protected ?int $message_id = null;
    protected ?string $data_key = null;
    protected ?string $data_value = null;

    /**
     * Constructor
     *
     * @param ?array $row Data array from query
     */
    public function __construct(?array $row)
    {
        // Load properties
        $this->message_id = isset($row['message_id']) ? $row['message_id'] : null;
        $this->data_key = isset($row['data_key']) ? $row['data_key'] : null;
        $this->data_value = isset($row['data_value']) ? (int) $row['data_value'] : null;

        parent::__construct($row);
    }
}
