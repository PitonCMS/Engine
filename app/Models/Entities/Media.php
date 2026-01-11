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
 * Piton Media Value Object
 */
class Media extends PitonEntity
{
    // Table Properties
    protected ?string $filename = null;
    protected ?int $width = null;
    protected ?int $height = null;
    protected ?string $feature = null;
    protected ?string $caption = null;
    protected ?string $mime_type = null;
    protected ?string $optimized = null;

    // Query Properties
    protected ?string $category_id_list = null;
    protected ?int $media_sort = null;

    // Derived properties calculated at runtime
    protected ?float $aspectRatio = null;
    protected ?string $orientation = null;
    protected ?string $featured = null;
    protected ?array $categories = null;

    /**
     * Constructor
     *
     * @param ?array $row Data array from query
     */
    public function __construct(?array $row)
    {
        // Load properties
        parent::__construct($row);

        // Load derived properties
        $this->featured = ($this->feature == 'Y') ? 'featured-img' : null;

        // Load calculated media properties
        if (isset($this->height) && $this->height > 0) {
            $this->aspectRatio = round($this->width / $this->height, 2);
            $this->orientation = ($this->aspectRatio > 1) ? 'landscape' : 'portrait';
        }
    }
}
