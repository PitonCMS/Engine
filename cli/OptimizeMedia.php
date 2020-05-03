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
class OptimizeMedia extends CLIBase
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
        $this->log("OptimizeMedia run() started");
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');

        // Although super unlikely, ensure the generated key is currently not in use by another process
        do {
            $this->key = ($this->container->filenameGenerator)();
        } while ($mediaMapper->optimizeKeyExists($this->key));

        $this->log("Optimized media key: {$this->key}");

        // Get media records that need optimizing
        $files = $mediaMapper->findNewMediaToOptimize($this->key);

        if (!$files) {
            // Nothing to optimize so exit
            $this->log("No media to optimize");
            exit(0);
        }

        // For each file, optimize media
        foreach ($files as $file) {
            if ($this->makeMediaSet($file->filename)) {
                // Update status on each record when complete
                $mediaMapper->setOptimizedStatus($file->id, $mediaMapper->getOptimizedCode('complete'));
                $this->log("Complete");
            } else {
                $mediaMapper->setOptimizedStatus($file->id, $mediaMapper->getOptimizedCode('retry'));
                $this->log("Failed - retry");
            }
        }

        $this->setAlert('info', 'Finished Optimizing Media');
        $this->log("Finished optimizing media.");
    }

    /**
     * Make Optimized Media Set
     *
     * @param  string $filename
     * @return bool
     */
    protected function makeMediaSet(string $filename): bool
    {
        $this->log("Optimizing $filename");

        // Ensure there is a Tinify API key
        if (!empty($this->container->get('settings')['site']['tinifyApiKey'])) {
            $mediaHandler = $this->container->mediaHandler;

            // Check if there was an issue constructing the MediaHandler and setting the Tinify key
            if ($mediaHandler->getErrorMessages()) {
                $this->setAlert('danger', 'Failed to set Tinify key to optimize media. ' . implode(' | ', $mediaHandler->getErrorMessages()));
                $this->log($mediaHandler->getErrorMessages());
                return false;
            }

            // Set the file source
            if (!$mediaHandler->setSource($filename)) {
                $this->setAlert('danger', 'Unable to load source media to optimize. ' . implode(' | ', $mediaHandler->getErrorMessages()));
                $this->log("Unable to set Tinify source");
                return false;
            }
            $this->log("Media source $filename set...");

            $mediaHandler->makeXLarge();
            $this->log("Finished $filename XLarge...");

            $mediaHandler->makeLarge();
            $this->log("Finished $filename Large...");

            $mediaHandler->makeSmall();
            $this->log("Finished $filename Small...");

            $mediaHandler->makeThumb();
            $this->log("Finished $filename Thumb...");

            return true;
        } else {
            $this->log("No Tinify key found");
            $this->setAlert('danger', 'Unable to Optimize Media', 'No Tinify key was found.');

            return false;
        }
    }
}
