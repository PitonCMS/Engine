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
     * Upload File
     */
    public function uploadFile()
    {
        $fileUpload = $this->container->fileUploadHandler;
        $mapper = $this->container->dataMapper;
        $mediaMapper = $mapper('MediaMapper');

        $fileUpload->upload('media-file');

        // Save reference to database
        $media = $mediaMapper->make();
        $media->file = $fileUpload->getFileName();
        $media->caption = $this->request->getParsedBodyParam('caption');
        $mediaMapper->save($media);

        return $this->redirect('adminFileUploadForm');
    }
}
