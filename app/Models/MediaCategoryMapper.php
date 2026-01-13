<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

namespace Piton\Models;

use Piton\ORM\DataMapperAbstract;

/**
 * Piton Media Category Mapper
 */
class MediaCategoryMapper extends DataMapperAbstract
{
    protected string $table = 'media_category';
    protected array $modifiableColumns = ['category'];
    protected string $domainValueObjectClass = __NAMESPACE__ . '\Entities\MediaCategory';

    /**
     * Find Categories
     *
     * Find all categories sorted by category name
     * @param  void
     * @return ?array
     */
    public function findCategories(): ?array
    {
        $this->makeSelect();
        $this->sql .= ' order by category';

        return $this->find();
    }
}
