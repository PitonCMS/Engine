<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
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

    // Derived Properties
    protected ?string $status = null;
}
