<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2020 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Controllers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Slim\Http\Response;
use Throwable;
use Exception;

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

        // Get all media and categories
        $media = $mediaMapper->findAllMedia() ?? [];
        $categories = $mediaCategoryMapper->findCategories() ?? [];
        $categories = array_column($categories, 'category', 'id');

        foreach ($media as &$medium) {
            $assignedCategories = ($medium->category_id_list) ? explode(',', $medium->category_id_list) : [];
            $medium->categories = array_fill_keys($assignedCategories, true);
        }

        $data['media'] = $media;
        $data['categories'] = $categories;

        return $this->render('media/media.html', $data);
    }

    /**
     * Get Media
     *
     * XHR asynchronous request
     * Gets filtered media rendered as HTML from template
     * @param  void
     * @return Response
     */
    public function getMedia(): Response
    {
        try {
            $mediaMapper = ($this->container->dataMapper)('MediaMapper');
            $data = $mediaMapper->find();

            $template = "
                {{ include(\"@admin/media/_mediaSearchControls.html\") }}
                {{ include(\"@admin/media/_mediaList.html\") }}
            ";

            $status = "success";
            $text = $this->container->view->fetchFromString($template, ['page' => ['media' => $data]]);
        } catch (Exception $th) {
            $status = "error";
            $text = "Exception getting data: {$th->getMessage()}";
        }

        return $this->xhrResponse($status, $text);
    }

    /**
     * Save Media
     *
     * XHR Response
     * Save media caption, categories
     * @param void
     * @return Response
     * @uses POST
     */
    public function saveMedia(): Response
    {
        try {
            $mediaMapper = ($this->container->dataMapper)('MediaMapper');
            $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');

            $media = $mediaMapper->make();
            $media->id = (int) $this->request->getParsedBodyParam('media_id');
            $media->caption = $this->request->getParsedBodyParam('caption');
            $media->feature = ($this->request->getParsedBodyParam('feature', false)) ? 'Y' : 'N';
            $mediaMapper->save($media);

            // Save category mappings
            $mediaCategoryMapper->saveMediaCategoryAssignments($media->id, $this->request->getParsedBodyParam('category'));

            $status = "success";
            $text = "Saved media";
        } catch (Exception $th) {
            $status = "error";
            $text = "Exception getting data: {$th->getMessage()}";
        }

        return $this->xhrResponse($status, $text);
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
     * Get Upload Form
     *
     * XHR Request
     * Gets upload form with current media categories
     * @param void
     * @return Response
     */
    public function getMediaUploadForm(): Response
    {
        // Wrap in try catch to stop processing at any point and let the xhrResponse takeover
        try {
            // Make string template
            $template = '{% import "@admin/media/_mediaMacros.html" as mediaMacro %}';
            $template .= ' {{ mediaMacro.uploadForm() }}';

            $status = "success";
            $text = $this->container->view->fetchFromString($template);
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception getting media file upload form: ". $th->getMessage();
        }

        return $this->xhrResponse($status, $text);
    }

    /**
     * Upload File
     *
     * XHR Request
     * @param void
     * @return Response
     */
    public function uploadMedia(): Response
    {
        // Wrap in try catch to stop processing at any point and let the xhrResponse takeover
        try {
            // Get dependencies
            $fileUpload = $this->container->fileUploadHandler;
            $mediaMapper = ($this->container->dataMapper)('MediaMapper');
            $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');
            $status = "success";
            $text = "File upload succeeded";

            // Try the upload
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
                // Failed to upload, so throw exception and return message to client
                throw new Exception("File Upload Failed: " . $fileUpload->getErrorMessage());
            }

            // Clear file upload
            $fileUpload->clear('media-file');
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception uploading file: ". $th->getMessage();
        }

        return $this->xhrResponse($status, $text);
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
     * @uses POST
     */
    public function saveMediaCategories(): Response
    {
        $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');
        $categoriesPost = $this->request->getParsedBodyParam('category');

        foreach ($categoriesPost as $cat) {
            // Skip if category name is empty
            if (empty(trim($cat['name']))) {
                continue;
            }

            // Make category object and save
            $category = $mediaCategoryMapper->make();
            $category->id = $cat['id'];
            $category->category = trim($cat['name']);
            $mediaCategoryMapper->save($category);
        }

        // Return to showing categories
        return $this->redirect('adminMediaCategoryEdit');
    }

    /**
     * Delete Media Category
     *
     * XHR Request
     * @param void
     * @return Response
     * @uses POST
     */
    public function deleteMediaCategory(): Response
    {
        // Wrap in try catch to stop processing at any point and let the xhrResponse takeover
        try {
            $mediaCategoryMapper = ($this->container->dataMapper)('MediaCategoryMapper');
            $categoryId = $this->request->getParsedBodyParam('categoryId');
            $status = "success";
            $text = "";

            if (is_numeric($categoryId)) {
                // Delete category assignments
                $mediaCategoryMapper->deleteMediaCategoryAssignmentsByCategoryId((int) $categoryId);

                // Delete category
                $category = $mediaCategoryMapper->make();
                $category->id = $categoryId;
                $mediaCategoryMapper->delete($category);

                // Foreign key constraints on media_category_map cascade delete to media associations
            }
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception deleting medica category: ". $th->getMessage();
        }

        return $this->xhrResponse($status, $text);
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
