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

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Slim\Http\Response;
use Throwable;

/**
 * Piton Media Controller
 *
 * Manages media files
 */
class AdminMediaController extends AdminBaseController
{
    /**
     * Show All Media
     *
     * @param void
     * @return Response
     */
    public function showMedia(): Response
    {
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');
        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');
        $pagination = $this->container->adminMediaPagination;

        // Get all media
        $data['media'] = $mediaMapper->findAllMedia($pagination->getLimit(), $pagination->getOffset()) ?? [];
        $pagination->setPagePath($this->container->router->pathFor('adminMedia'));

        // Setup pagination
        $pagination->setTotalResultsFound($mediaMapper->foundRows() ?? 0);
        $this->container->view->addExtension($pagination);

        $data['assignedCategories'] = $mediaCategoryMapper->findCategories() ?? [];
        $categoryAssignments = $mediaCategoryMapper->findAllMediaCategoryAssignments() ?? [];

        // Identify any media ID's assigned to each category
        foreach ($data['assignedCategories'] as &$cat) {
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
     * @param void
     * @return Response
     */
    public function saveMedia(): Response
    {
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');
        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');

        $media = $mediaMapper->make();
        $media->id = (int) $this->request->getParsedBodyParam('media_id');
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

        return $this->redirect('adminMedia');
    }

    /**
     * Delete Media
     *
     * Deletes file and media record
     * @param void
     * @return Response
     */
    public function deleteMedia(): Response
    {
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');

        // Get the media record
        if (null !== $id = (int) $this->request->getParsedBodyParam('media_id')) {
            $mediaFile = $mediaMapper->findById($id);

            if (is_string($mediaFile->filename)) {
                // Delete all files and directory, then delete database record
                $dirToDelete = ($this->container->mediaPathHandler)($mediaFile->filename);
                $path = ROOT_DIR . 'public' . $dirToDelete;
                $this->deleteRecursive($path);

                $mediaMapper->delete($mediaFile);
            }
        }

        // Ajax response
        if ($this->request->isXhr()) {
            $r = $this->response->withHeader('Content-Type', 'application/json');
            return $r->write(json_encode(["status" => "success"]));
        }

        return $this->redirect('adminMedia');
    }

    /**
     * Get All Media
     *
     * XHR asynchronous response
     * Gets filtered media rendered as HTML from template
     * @param  void
     * @return Response
     */
    public function getMedia(): Response
    {
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');

        try {
            // Find media and render template
            $data = $mediaMapper->find();

            $template = "
            {% import \"@admin/media/_mediaMacros.html\" as mediaMacro %}
            {% for media in page.media %}
              {{ mediaMacro.card(media) }}
            {% endfor %}";

            $status = "success";
            $text = $this->container->view->fetchFromString($template, ['page' => ['media' => $data]]);
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception getting data: {$th->getMessage()}";
        }

        return $this->xhrResponse($status, $text);
    }

    /**
     * Upload File
     *
     * @param void
     * @return Response
     */
    public function uploadMedia(): Response
    {
        $fileUpload = $this->container->fileUploadHandler;
        $mediaMapper = ($this->container->dataMapper)('MediaMapper');
        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');

        if ($fileUpload->upload('media-file')) {
            // Set image optimization flag by request, and if a Tinyfy key exists and is compressible by Tinyfy
            $doOptimize = false;
            if (
                $this->request->getParsedBodyParam('optimize', null) === 'on' &&
                !empty($this->siteSettings['tinifyApiKey']) &&
                in_array($fileUpload->mimeType, ['image/png', 'image/jpeg'])
            ) {
                $doOptimize = true;
            }

            // Save media record to database
            $media = $mediaMapper->make();
            $media->filename = $fileUpload->getFilename();
            $media->caption = $this->request->getParsedBodyParam('caption');
            $media->width = ($fileUpload->width) ?: null;
            $media->height = ($fileUpload->height) ?: null;
            $media->feature = ($this->request->getParsedBodyParam('feature', false)) ? 'Y' : 'N';
            $media->mime_type = $fileUpload->mimeType;
            $media->optimized = ($doOptimize) ? $mediaMapper->getOptimizedCode('new') : $mediaMapper->getOptimizedCode('exclude');
            $mediaMapper->save($media);

            // Save category assignments
            $mediaCategoryMapper->saveMediaCategoryAssignments($media->id, $this->request->getParsedBodyParam('category'));

            // Optimize media uploads
            if ($doOptimize) {
                $this->optimizeNewMedia();
            }
        } else {
            $this->setAlert('danger', 'File Upload Failed', $fileUpload->getErrorMessage());
        }

        // Clear file upload
        $fileUpload->clear('media-file');

        return $this->redirect('adminMedia');
    }

    /**
     * Edit Media Categories
     *
     * @param void
     * @return Response
     */
    public function editMediaCategories(): Response
    {
        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');

        $data = $mediaCategoryMapper->find();

        return $this->render('media/mediaCategories.html', ['categories' => $data]);
    }

    /**
     * Save Media Categories
     *
     * @param void
     * @return Response
     */
    public function saveMediaCategories(): Response
    {
        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');
        $categoriesPost = $this->request->getParsedBody();

        foreach ($categoriesPost['category_name'] as $key => $cat) {
            // Skip if category name is empty
            if (empty($cat)) {
                continue;
            }

            // Make category object
            $category = $mediaCategoryMapper->make();
            $category->id = (int) $categoriesPost['category_id'][$key];

            // Check if we need to delete a category, but only if this has been previously saved with an ID
            if (isset($categoriesPost['delete'][$key]) && !empty($categoriesPost['category_id'][$key])) {
                $mediaCategoryMapper->delete($category);
            }

            // Save
            $category->category = $cat;
            $mediaCategoryMapper->save($category);
        }

        // Return to showing categories
        return $this->redirect('adminMediaCategoryEdit');
    }

    /**
     * Delete Media Category
     *
     * @param void
     * @return Response
     */
    public function deleteMediaCategory(): Response
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
     * Deletes entire chain of directories for media file path
     * @param  string $dir Media file base folder to delete
     * @return void
     */
    protected function deleteRecursive(string $dir): void
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
     * Optimize New Media Files
     *
     * Submits background process to optimize new media
     * @param void
     * @return void
     */
    protected function optimizeNewMedia()
    {
        // Submit background process to continue to run after this request returns
        $script = ROOT_DIR . 'vendor/pitoncms/engine/cli/cli.php';
        exec("php $script optimizeMedia  > /dev/null &");
    }
}
