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
     * Public Root Directory
     * @var string
     */
    protected $publicRoot;

    /**
     * New File Name
     * @var string
     */
    protected $filename;

    /**
     * Extension
     * @var string
     */
    protected $extension;

    /**
     * Media File Width
     * @var int
     */
    public $width;

    /**
     * Media File Height
     * @var int
     */
    public $height;

    /**
     * Aspect Ratio
     * @var float
     */
    protected $aspectRatio;

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
     * Media File URI Closure
     * @var closure
     */
    protected $mediaUri;

    /**
     * Media Sizes Closure
     * @var closure
     */
    protected $mediaSizes;

    /**
     * Constructor
     *
     * @param  closure $mediaUri     Function to derive media file URI
     * @param  closure $mediaSizes   Function to derive media size suffix
     * @param  string  $tinifyApiKey TinyJPG API Key
     * @return void
     */
    public function __construct(closure $mediaUri, closure $mediaSizes, string $tinifyApiKey)
    {
        // Make sure there is a key
        if (empty($tinifyApiKey)) {
            throw new Exception('PitonCMS: Media Handler requires a TinyJPG developer API Key');
        }

        try {
            \Tinify\setKey($tinifyApiKey);
            \Tinify\validate();
        } catch (\Tinify\Exception $e) {
            throw new Exception('PitonCMS: Invalid TinyJPG key submitted: ' . $e->getMessage());
        }

        $this->publicRoot = ROOT_DIR . 'public';
        $this->mediaUri = $mediaUri;
        $this->mediaSizes = $mediaSizes;
    }

    /**
     * Set Media Source
     *
     * @param string $soureMedia Source Filename
     * @return void
     */
    public function setSource(string $sourceMedia): void
    {
        // Construct media path, ignoring the first two pathinfo return elements
        $parts = pathinfo($sourceMedia);
        $this->extension = $parts['extension'];
        $this->filename = $parts['filename'];
        $absoluteMediaPath = $this->publicRoot . $this->getFileUri() . $sourceMedia;

        // Ensure source file exists
        if (!file_exists($absoluteMediaPath)) {
            throw new Exception('PitonCMS: Media Handler: Source file not found ' . $absoluteMediaPath);
        }

        // Get dimensions and set orientation
        list($this->width, $this->height) = getimagesize($absoluteMediaPath);

        if (isset($this->height) && $this->height > 0) {
            $this->aspectRatio = round($this->width / $this->height, 2);
            $this->orientation = ($this->aspectRatio > 1) ? 'landscape' : 'portrait';
        }

        $this->tinifySource = \Tinify\fromFile($absoluteMediaPath);
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

        $filename = $this->publicRoot . $this->getFileUri() . $this->filename . ($this->mediaSizes)('xlarge') . '.' . $this->extension;
        $this->tinifySource->toFile($filename);
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

        $filename = $this->publicRoot . $this->getFileUri() . $this->filename . ($this->mediaSizes)('large') . '.' . $this->extension;

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

        $resized = $this->tinifySource->resize($resize);
        $resized->toFile($filename);
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

        $filename = $this->publicRoot . $this->getFileUri() . $this->filename . ($this->mediaSizes)('small') . '.' . $this->extension;

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

        $resized = $this->tinifySource->resize($resize);
        $resized->toFile($filename);
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

        $filename = $this->publicRoot . $this->getFileUri() . $this->filename . ($this->mediaSizes)('thumb') . '.' . $this->extension;
        $resized = $this->tinifySource->resize([
            'method' => 'thumb',
            'width' => ($this->orientation === 'landscape') ? 350 : 265,
            'height' => ($this->orientation === 'landscape') ? 265 : 350
        ]);
        $resized->toFile($filename);
    }

    /**
     * Get File URI
     *
     * Derive file URI based on file name
     * @param  void
     * @return string
     */
    public function getFileUri(): string
    {
        return ($this->mediaUri)($this->filename);
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
