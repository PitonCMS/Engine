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
}
