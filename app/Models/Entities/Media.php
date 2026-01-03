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
    // Assigned Properties
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


    /**
     * Constructor
     *
     * @param ?array $row Data array from query
     */
    public function __construct(?array $row)
    {
        // Load properties
        $this->filename = isset($row['filename']) ? $row['filename'] : null;
        $this->width = isset($row['width']) ? (int) $row['width'] : null;
        $this->height = isset($row['height']) ? (int) $row['height'] : null;
        $this->feature = isset($row['feature']) ? $row['feature'] : null;
        $this->caption = isset($row['caption']) ? $row['caption'] : null;
        $this->mime_type = isset($row['mime_type']) ? $row['mime_type'] : null;
        $this->optimized = isset($row['optimized']) ? $row['optimized'] : null;
        $this->category_id_list = isset($row['category_id_list']) ? $row['category_id_list'] : null;

        // Calculate and load derived properties
        if (isset($this->height) && $this->height > 0) {
            $this->aspectRatio = round($this->width / $this->height, 2);
            $this->orientation = ($this->aspectRatio > 1) ? 'landscape' : 'portrait';
        }

        $this->featured = ($this->feature == 'Y') ? 'featured-img' : null;

        // If a "media_id" is passed in, then set that as the record $id before calling parent::__construct()
        // TODO Test if needed
        // if (isset($row['media_id'])) {
        //     $row['id'] = (int) $row['media_id'];
        // }

        parent::__construct($row);
    }
}
