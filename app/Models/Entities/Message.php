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

    // Calculated Properties
    protected ?int $unread = null;
}
