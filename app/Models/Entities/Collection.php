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
