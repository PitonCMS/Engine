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
 * Piton Media Category Map Value Object
 */
class MediaCategoryMap extends PitonEntity
{
    // Assigned Properties
    protected ?int $media_id = null;
    protected ?int $category_id = null;
    protected ?int $media_sort = null;
}
