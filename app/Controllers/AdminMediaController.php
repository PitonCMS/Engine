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
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');

        $data = $mediaMapper->find();

        return $this->render('media.html', ['media' => $data]);
    }

    /**
     * Get All Media
     *
     * Gets all media asynchronously with HTML
     */
    public function getMedia()
    {
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');

        $data = $mediaMapper->find();

        $template = <<<HTML
            {% import "@admin/includes/_mediaCardMacro.html" as file %}
            <div class="card-wrapper">
            {% for media in page.media %}
              {{ file.mediaCard(media) }}
            {% endfor %}
            </div>
HTML;

        $mediaHtml = $this->container->view->fetchFromString($template, ['page' => ['media' => $data]]);

        $response = $this->response->withHeader('Content-Type', 'application/json');

        return $response->write(json_encode(["html" => $mediaHtml]));
    }

    /**
     * Upload File
     */
    public function uploadFile()
    {
        $fileUpload = $this->container->fileUploadHandler;
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');

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
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');

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
     * Edit Media Categories
     */
    public function editMediaCategories()
    {
        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');

        $data = $mediaCategoryMapper->find();

        return $this->render('mediaCategories.html', ['categories' => $data]);
    }

    /**
     * Save Media Categories
     */
    public function saveMediaCategories()
    {
        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');

        $categoriesPost = $this->request->getParsedBody();

        foreach ($categoriesPost['category'] as $key => $cat) {
            // Skip if category name is empty
            if (empty($cat)) {
                continue;
            }

            // Make category object
            $category = $mediaCategoryMapper->make();
            $category->id = $categoriesPost['id'][$key];

            // Check if we need to delete a category, but only if this has been previously saved with an ID
            if (isset($categoriesPost['delete'][$key]) && !empty($categoriesPost['id'][$key])) {
                $mediaCategoryMapper->delete($category);
            }

            // Save
            $category->category = $cat;
            $mediaCategoryMapper->save($category);
        }

        // Return to showing categories
        return $this->redirect('adminEditMediaCategories');
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
