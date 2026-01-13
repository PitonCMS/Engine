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
 * Piton Collection Value Object
 */
class Collection extends PitonEntity
{
    // Assigned Properties
    protected ?string $collection_slug = null;
    protected ?string $collection_title = null;
    protected ?string $collection_definition = null;
    protected ?int $page_count = null;
}
