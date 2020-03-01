<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Library\Handlers;

use Exception;
use Closure;

/**
 * Piton Media Handler
 *
 * Manages media manipulations and optimizations
 * Requires TinyJPG API
 */
class Media
{
    /**
     * Provided File Name
     * @var string
     */
    protected $filename;

    /**
     * Absolute File Path
     * @var string
     */
    protected $mediaPath;

    /**
     * Media File Width
     * @var int
     */
    protected $width;

    /**
     * Media File Height
     * @var int
     */
    protected $height;

    /**
     * Orientation
     * @var string
     */
    protected $orientation;

    /**
     * Tinify Source
     * @var \Tinify\Source
     */
    protected $tinifySource;

    /**
     * Media File Path Closure
     * @var closure
     */
    protected $mediaPathClosure;

    /**
     * Media Sizes Closure
     * @var closure
     */
    protected $mediaSizesClosure;

    /**
     * Constructor
     *
     * @param  closure $mediaPath    Function to derive media file Path
     * @param  closure $mediaSizes   Function to derive media size suffix
     * @param  string  $tinifyApiKey TinyJPG API Key
     * @return void
     * @throws Exception
     */
    public function __construct(closure $mediaPath, closure $mediaSizes, string $tinifyApiKey)
    {
        // Make sure there is a valid key
        try {
            \Tinify\setKey($tinifyApiKey);
            \Tinify\validate();
        } catch (\Tinify\Exception $e) {
            throw new Exception('PitonCMS: Invalid TinyJPG key submitted: ' . $e->getMessage());
        }

        $this->mediaPathClosure = $mediaPath;
        $this->mediaSizesClosure = $mediaSizes;
    }

    /**
     * Set Media Source
     *
     * @param string $soureMedia Source media filename
     * @return void
     * @throws Exception
     */
    public function setSource(string $sourceMedia): void
    {
        // Get path to file and keep as property
        $this->filename = $sourceMedia;
        $this->mediaPath = ROOT_DIR . 'public' . ($this->mediaPathClosure)($sourceMedia);
        $absoluteSourceMedia = $this->mediaPath . $sourceMedia;

        // Ensure source file exists
        if (!file_exists($absoluteSourceMedia)) {
            throw new Exception('PitonCMS: Media Handler: Source file not found ' . $absoluteSourceMedia);
        }

        // Get dimensions and set orientation
        list($this->width, $this->height) = getimagesize($absoluteSourceMedia);

        if (isset($this->height) && $this->height > 0) {
            $aspectRatio = round($this->width / $this->height, 2);
            $this->orientation = ($aspectRatio > 1) ? 'landscape' : 'portrait';
        }

        $this->tinifySource = \Tinify\fromFile($absoluteSourceMedia);
    }

    /**
     * Make Optimized X-Large Media File
     *
     * Uses Tinify optimization
     * @param void
     * @return void
     */
    public function makeXLarge(): void
    {
        $this->validateTinifySource();

        $this->tinifySource->toFile($this->getAbsoluteFilenameBySize('xlarge'));
    }

    /**
     * Make Optimized Large Media File
     *
     * Uses Tinify optimization
     * @param void
     * @return void
     */
    public function makeLarge(): void
    {
        $this->validateTinifySource();

        // If square, keep aspect and constrain to 2000 x 2000
        if ($this->width == $this->height) {
            $resize = [
                'method' => 'fit',
                'width' => 2000,
                'height' => 2000
            ];
        } elseif ($this->orientation === 'landscape') {
            $resize = [
                'method' => 'fit',
                'width' => 2000,
                'height' => 1500
            ];
        } else {
            $resize = [
                'method' => 'fit',
                'width' => 1500,
                'height' => 2000
            ];
        }

        $this->tinifySource->resize($resize)->toFile($this->getAbsoluteFilenameBySize('large'));
    }

    /**
     * Make Optimized Small Media File
     *
     * Uses Tinify optimization
     * @param void
     * @return void
     */
    public function makeSmall(): void
    {
        $this->validateTinifySource();

        // If square, keep aspect and constrain to 2000 x 2000
        if ($this->width == $this->height) {
            $resize = [
                'method' => 'fit',
                'width' => 2000,
                'height' => 2000
            ];
        } elseif ($this->orientation === 'landscape') {
            $resize = [
                'method' => 'fit',
                'width' => 1024,
                'height' => 768
            ];
        } else {
            $resize = [
                'method' => 'fit',
                'width' => 768,
                'height' => 1024
            ];
        }

        $this->tinifySource->resize($resize)->toFile($this->getAbsoluteFilenameBySize('small'));
    }

    /**
     * Make Optimized Thumbnail Media File
     *
     * Uses Tinify optimization
     * @param void
     * @return void
     */
    public function makeThumb(): void
    {
        $this->validateTinifySource();

        $this->tinifySource->resize([
            'method' => 'thumb',
            'width' => ($this->orientation === 'landscape') ? 350 : 265,
            'height' => ($this->orientation === 'landscape') ? 265 : 350
        ])->toFile($this->getAbsoluteFilenameBySize('thumb'));
    }

    /**
     * Get Absolute Filename by Size
     *
     * Returns absolute filename with size and extension
     * @param string $size
     * @return string
     */
    protected function getAbsoluteFilenameBySize(string $size = ''): string
    {
        return $this->mediaPath . ($this->mediaSizesClosure)($this->filename, $size);
    }

    /**
     * Validate Tinify Source is Set
     *
     * @param void
     * @return void
     * @throws Exception
     */
    protected function validateTinifySource(): void
    {
        if (!$this->tinifySource instanceof \Tinify\Source) {
            throw new Exception('PitonCMS: Media setSource() has not been set with a valid media file.');
        }
    }
}
