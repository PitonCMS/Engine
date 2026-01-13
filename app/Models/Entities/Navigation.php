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
 * Piton Navigation Value Object
 */
class Navigation extends PitonEntity
{
    // Class Properties
    protected ?string $navigator = null;
    protected ?int $parent_id = null;
    protected ?int $sort = null;
    protected ?int $page_id = null;
    protected ?int $collection_id = null;
    protected ?string $title = null;
    protected ?string $url = null;

    // Derived properties
    protected ?string $nav_title = null;
    protected ?string $page_title = null;
    protected ?string $collection_title = null;
    protected ?string $collection_slug = null;
    protected ?string $published_date = null;
    protected ?string $page_slug = null;
    protected ?bool $currentPage = null;
    protected ?array $childNav = null;
}
