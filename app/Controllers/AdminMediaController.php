<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Controllers;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Piton Media Controller
 *
 * Manages media files
 */
class AdminMediaController extends AdminBaseController
{
    /**
     * Show All Media
     */
    public function showMedia()
    {
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');
        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');

        $data['media'] = $mediaMapper->findAllMedia();
        $data['categories'] = $mediaCategoryMapper->findCategories();
        $categoryAssignments = $mediaCategoryMapper->findAllMediaCategoryAssignments();

        // Identify any media ID's assigned to each category
        foreach ($data['categories'] as &$cat) {
            $cat->media = [];
            foreach ($categoryAssignments as $map) {
                if ($cat->id === $map->category_id) {
                    $cat->media[$map->media_id] = 'on';
                }
            }
        }

        return $this->render('media/media.html', $data);
    }

    /**
     * Save Media
     *
     * Save media caption, categories
     */
    public function saveMedia()
    {
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');
        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');

        $media = $mediaMapper->make();
        $media->id = $this->request->getParsedBodyParam('id');
        $media->caption = $this->request->getParsedBodyParam('caption');
        $media->feature = ($this->request->getParsedBodyParam('feature', false)) ? 'Y' : 'N';
        $mediaMapper->save($media);

        // Save category mappings
        $mediaCategoryMapper->saveMediaCategoryAssignments($media->id, $this->request->getParsedBodyParam('category'));

        // Set the response type
        if ($this->request->isXhr()) {
            $r = $this->response->withHeader('Content-Type', 'application/json');
            return $r->write(json_encode(["status" => "success"]));
        }

        return $this->redirect('adminShowMedia');
    }

    /**
     * Delete Media
     *
     * Deletes file and media record
     * @param
     * @return void
     */
    public function deleteMedia()
    {
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');

        // Get the media record
        if (null !== $id = $this->request->getParsedBodyParam('id')) {
            $mediaFile = $mediaMapper->findById((int) $id);

            if (is_string($mediaFile->filename)) {
                $rootDir = substr($mediaFile->filename, 0, 2);
                $this->deleteRecursive(ROOT_DIR . 'public/media/' . $rootDir);

                $mediaMapper->delete($mediaFile);
            }
        }

        // Set the response type
        if ($this->request->isXhr()) {
            $r = $this->response->withHeader('Content-Type', 'application/json');
            return $r->write(json_encode(["status" => "success"]));
        }

        return $this->redirect('adminShowMedia');
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
            {% import "@admin/media/_mediaCardMacro.html" as file %}
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
    public function uploadMedia()
    {
        $fileUpload = $this->container->fileUploadHandler;
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');
        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');

        if ($fileUpload->upload('media-file')) {
            // Save media reference to database
            $media = $mediaMapper->make();
            $media->filename = $fileUpload->getFilename();
            $media->caption = $this->request->getParsedBodyParam('caption');
            $media->width = ($fileUpload->width) ?: null;
            $media->height = ($fileUpload->height) ?: null;
            $media->feature = ($this->request->getParsedBodyParam('feature', false)) ? 'Y' : 'N';
            $mediaMapper->save($media);

            // Save category assignments
            $mediaCategoryMapper->saveMediaCategoryAssignments($media->id, $this->request->getParsedBodyParam('category'));

            // Make optimized copies
            $this->makeMediaSet($fileUpload->getFilename());
        } else {
            $this->setAlert('danger', 'File Upload Failed', $fileUpload->getErrorMessage());
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

        return $this->render('media/mediaCategories.html', ['categories' => $data]);
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
     * Delete Media Category
     *
     */
    public function deleteMediaCategory()
    {
        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');
        $categoryId = (int) $this->request->getParsedBodyParam('id');
        $status = 'error';

        if (!empty($categoryId) && is_int($categoryId)) {
            // Delete category assignments
            $mediaCategoryMapper->deleteMediaCategoryAssignmentsByCategoryId($categoryId);

            // Delete category
            $category = $mediaCategoryMapper->make();
            $category->id = $categoryId;
            $mediaCategoryMapper->delete($category);
            $status = 'success';
        }

        $r = $this->response->withHeader('Content-Type', 'application/json');
        return $r->write(json_encode(["status" => $status]));
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

    /**
     * Make Optimized Media Set
     *
     * @param  string $filename
     * @return void
     */
    protected function makeMediaSet($filename)
    {
        // Ensure there is a Tinify API key
        if (($this->container->settings)['site']['tinifyApiKey']) {
            $mediaHandler = $this->container->mediaHandler;

            $mediaHandler->setSource($filename);
            $mediaHandler->makeXLarge();
            $mediaHandler->makeLarge();
            $mediaHandler->makeSmall();
            $mediaHandler->makeThumb();
        }
    }
}
