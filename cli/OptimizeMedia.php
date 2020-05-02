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
     * @param void
     * @param void
     */
    public function run()
    {
        $this->print("Optimizing media image files...");
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');

        // Although super unlikely, ensure the generated key is currently not in use by another process
        do {
            $this->key = ($this->container->filenameGenerator)();
        } while ($mediaMapper->optimizeKeyExists($this->key));

        $this->print("Optimizing key: {$this->key}");
        $this->container->logger->info("PitonCLI: Background process to optimized media started for key: {$this->key}");

        // Get query of media records that need optimizing
        $files = $mediaMapper->findNewMediaToOptimize($this->key);

        if (!$files) {
            // Nothing to optimize so exit
            $this->print('No media to optimize');
            exit(0);
        }

        // For each file, optimize media
        foreach ($files as $file) {
            if ($this->makeMediaSet($file->filename)) {
                // Update status on each record when complete
                $mediaMapper->setOptimizedStatus($file->id, $mediaMapper->getOptimizedCode('complete'));
            } else {
                $mediaMapper->setOptimizedStatus($file->id, $mediaMapper->getOptimizedCode('retry'));
            }
        }

        $this->setAlert('info', 'Finished Optimizing Media');
        $this->print("Finished optimizing media.");
    }

    /**
     * Make Optimized Media Set
     *
     * @param  string $filename
     * @return bool
     */
    protected function makeMediaSet(string $filename): bool
    {
        // Ensure there is a Tinify API key
        if (!empty($this->container->get('settings')['site']['tinifyApiKey'])) {
            $mediaHandler = $this->container->mediaHandler;

            $mediaHandler->setSource($filename);
            $mediaHandler->makeXLarge();
            $mediaHandler->makeLarge();
            $mediaHandler->makeSmall();
            $mediaHandler->makeThumb();

            return true;
        } else {
            $this->setAlert('danger', 'Unable to Optimize Media', 'No Tinify key was found.');

            return false;
        }
    }
}
