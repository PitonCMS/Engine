<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
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

    // Derived Properties
    protected ?string $inputs = null;
}
