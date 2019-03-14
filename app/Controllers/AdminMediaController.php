<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Controllers;

use \FilesystemIterator;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;

/**
 * Piton Media Controller
 *
 * Manages media files
 */
class AdminMediaController extends AdminBaseController
{
    /**
     * Upload File Form
     *
     */
    public function uploadFileForm()
    {
        return $this->render('uploadFileForm.html');
    }

    /**
     * Show All Media
     */
    public function showMedia()
    {
        $mapper = $this->container->dataMapper;
        $mediaMapper = $mapper('MediaMapper');

        $data = $mediaMapper->find();

        return $this->render('media.html', ['media' => $data]);
    }

    /**
     * Upload File
     */
    public function uploadFile()
    {
        $fileUpload = $this->container->fileUploadHandler;
        $mapper = $this->container->dataMapper;
        $mediaMapper = $mapper('MediaMapper');

        if ($fileUpload->upload('media-file')) {
            // Save reference to database
            $media = $mediaMapper->make();
            $media->file = $fileUpload->getFileName();
            $media->caption = $this->request->getParsedBodyParam('caption');
            $mediaMapper->save($media);
        } else {
            $this->setAlert('danger', 'File Upload Failed', $fileUpload->getErrorMessage());
        }

        return $this->redirect('adminFileUploadForm');
    }

    /**
     * Delete File
     *
     * Deletes file and media record
     * @param
     * @return void
     */
    public function deleteFile()
    {
        $mapper = $this->container->dataMapper;
        $mediaMapper = $mapper('MediaMapper');

        // Get the media record
        if (null !== $id = $this->request->getParsedBodyParam('id')) {
            $mediaFile = $mediaMapper->findById($id);

            if (is_string($mediaFile->file)) {
                $rootDir = substr($mediaFile->file, 0, 2);
                $this->deleteRecursive(ROOT_DIR . 'public/media/' . $rootDir);

                $mediaMapper->delete($mediaFile);
            }
        }

        return $this->redirect('adminShowMedia');
    }

    /**
     * Recursively Delete File and Directory
     *
     * Deletes entire chain of directories for one media file path
     * @param  string $dir Media file base folder to delete
     * @return void
     */
    protected function deleteRecursive($dir)
    {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dir);
    }
}
