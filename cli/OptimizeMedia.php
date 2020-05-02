<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2020 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\CLI;

use Psr\Container\ContainerInterface;

/**
 * Piton Optimize Media CLI
 *
 * Runs as background process on request, to optimize media files
 */
class OptimizeMedia extends Base
{
    /**
     * Optimize Key
     * @var string
     */
    public $key;

    /**
     * Run Optimizer
     *
     *
     */
    public function run()
    {
        $this->print("running...");
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');
        $this->container->logger->info("PitonCLI: Background process to optimized media started for key: {$this->key}");

        // Although super unlikely, ensure the key is currently not in use by another process
        do {
            $this->key = ($this->container->filenameGenerator)();
        } while ($mediaMapper->optimizeKeyExists($this->key));

        // Get query of media records that need optimizing
        $files = $mediaMapper->findNewMediaToOptimize($this->key);

        if (!$files) {
            // Nothing to optimize so exit
            $this->print('No media to optimize');
            exit(0);
        }

        // For each, optimize media
        foreach ($files as $key => $file) {
            if ($this->optimize($file->filename)) {
                $this->print("updating status $file->id");
                $output = $mediaMapper->setOptimizedStatus($file->id);
                $this->print($output);
            }
        }

        // - Update each record when complete

        // End
    }

    protected function optimize($filename)
    {
        $this->print("Optimizing $filename ...");
        sleep(5);
        return true;
    }

    /**
     * Make Optimized Media Set
     *
     * @param  string $filename
     * @return void
     */
    protected function makeMediaSet(string $filename): void
    {
        // Ensure there is a Tinify API key
        if (!empty($this->siteSettings['tinifyApiKey'])) {
            $mediaHandler = $this->container->mediaHandler;

            $mediaHandler->setSource($filename);
            $mediaHandler->makeXLarge();
            $mediaHandler->makeLarge();
            $mediaHandler->makeSmall();
            $mediaHandler->makeThumb();
        }
    }
}
