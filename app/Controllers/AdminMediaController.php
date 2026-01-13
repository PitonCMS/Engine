<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

namespace Piton\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
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
        $data = $this->loadMedia();

        return $this->render('media/media.html', $data);
    }

    /**
     * Get Media Search Controls
     *
     * XHR asynchronous request
     * Loads media search controls for use in media select modals
     * @param void
     * @return Response
     */
    public function getMediaSearchControls(): Response
    {
        try {
            $template = <<<HTML
                {{ include("@admin/media/_mediaSearchControls.html") }}
                <div class="media-wrapper" data-query="content"></div>
HTML;

            $status = "success";
            $text = $this->view->fetchFromString($template);
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception getting media controls: {$th->getMessage()}";
        }

        return $this->xhrResponse($status, $text);
    }

    /**
     * Get Media
     *
     * XHR asynchronous request
     * Gets filtered media rendered as HTML from template
     * @param array $args['context']: edit | static
     * @param  void
     * @return Response
     */
    public function getMedia(array $args): Response
    {
        try {
            $macro = ($args['context'] === "edit") ? "cardEdit" : "cardStatic";
            $data = $this->loadMedia();
            $template = <<<HTML
                {% import "@admin/media/_mediaMacros.html" as mediaMacro %}
                {% for medium in media %}
                    {{ mediaMacro.$macro(medium, categories) }}
                {% endfor %}
                {{ pagination() }}
HTML;

            $status = "success";
            $text = $this->view->fetchFromString($template, ['media' => $data['media'], 'categories' => $data['categories']]);
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception getting data: {$th->getMessage()}";
        }

        return $this->xhrResponse($status, $text);
    }

    /**
     * Load Media
     *
     * Get all media using query string parameters
     * @param void
     * @param array
     * @uses GET params
     */
    protected function loadMedia(): array
    {
        // Load dependencies
        $mediaMapper = ($this->container->get('dataMapper'))('MediaMapper');
        $mediaCategoryMapper = ($this->container->get('dataMapper'))('MediaCategoryMapper');
        $pagination = $this->getPagination();
        $pagination->setPagePath($this->container->get('router')->urlFor('adminMedia'));

        // Get filters or search if requested
        $category = htmlspecialchars($this->getQueryParam('category', '0'));
        $featured = htmlspecialchars($this->getQueryParam('featured', 'all'));
        $terms = htmlspecialchars($this->getQueryParam('terms', ''));

        // Get data
        if (!empty($terms)) {
            // This is a search request and takes precedence
            $media = $mediaMapper->searchMedia($terms, $pagination->getLimit(), $pagination->getOffset()) ?? [];
            $pagination->setTotalResultsFound($mediaMapper->foundRows() ?? 0);
        } elseif ($category !== '0' || $featured !== 'all') {
            // Return filtered list by category ID and featured flag
            // Filter by category has no pagination so that one can see all media in the category to reorder
            $media = $mediaMapper->findMediaByCategoryIdAndFeatured((int) $category, $featured) ?? [];
        } else {
            // Get all media
            $media = $mediaMapper->findAllMedia($pagination->getLimit(), $pagination->getOffset()) ?? [];
            $pagination->setTotalResultsFound($mediaMapper->foundRows() ?? 0);
        }

        // Load and assign media categories
        $categories = $mediaCategoryMapper->findCategories() ?? [];
        $categories = array_column($categories, 'category', 'id');

        foreach ($media as &$medium) {
            $assignedCategories = ($medium->category_id_list) ? explode(',', $medium->category_id_list) : [];
            $medium->categories = array_fill_keys($assignedCategories, true);
        }

        $data['media'] = $media;
        $data['categories'] = $categories;

        return $data;
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
            $mediaMapper = ($this->container->get('dataMapper'))('MediaMapper');
            $mediaCategoryMapMapper = ($this->container->get('dataMapper'))('MediaCategoryMapMapper');

            $media = $mediaMapper->make();
            $media->id = $this->getParsedBodyParam('media_id');
            $media->caption = $this->getParsedBodyParam('caption');
            $media->feature = ($this->getParsedBodyParam('feature', false)) ? 'Y' : 'N';
            $mediaMapper->save($media);

            // Save category mappings
            $mediaCategoryMapMapper->saveMediaCategoryAssignments($media->id, $this->getParsedBodyParam('category'));

            $status = "success";
            $text = "Saved media";
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception getting data: {$th->getMessage()}";
        }

        return $this->xhrResponse($status, $text);
    }

    /**
     * Delete Media
     *
     * XHR Request
     * Deletes file and media record
     * @param void
     * @return Response
     * @uses POST
     */
    public function deleteMedia(): Response
    {
        try {
            $mediaMapper = ($this->container->get('dataMapper'))('MediaMapper');

            // Get the media record
            $id = (int) $this->getParsedBodyParam('media_id');
            $mediaFile = $mediaMapper->findById($id);

            if (is_string($mediaFile->filename)) {
                // Delete all related files and directory, then delete database record
                $dirToDelete = ($this->container->get('mediaPathHandler'))($mediaFile->filename);
                $path = ROOT_DIR . 'public' . $dirToDelete;
                $this->deleteRecursive($path);
            }

            // Foreign key constraints on media_category_map cascade delete from media table
            $mediaMapper->delete($mediaFile);

            $status = "success";
            $text = "Deleted media id $id, file {$mediaFile->filename}";
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception deleting media: {$th->getMessage()}";
        }

        return $this->xhrResponse($status, $text);
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
            $text = $this->view->fetchFromString($template);
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
            $fileUpload = $this->container->get('fileUploadHandler');
            $mediaMapper = ($this->container->get('dataMapper'))('MediaMapper');
            $mediaCategoryMapMapper = ($this->container->get('dataMapper'))('MediaCategoryMapMapper');
            $status = "success";
            $text = "File upload succeeded";

            // Upload the files from the POST Request
            $fileUpload->setUploadedFiles($this->request->getUploadedFiles());

            // Try the upload
            if ($fileUpload->upload('media-file')) {
                // Set image optimization flag by request, and if a Tinyfy key exists and is compressible by Tinyfy
                $doOptimize = false;
                if (
                    $this->getParsedBodyParam('optimize') === 'on' &&
                    !empty($this->settings['site']['tinifyApiKey']) &&
                    in_array($fileUpload->mimeType, ['image/png', 'image/jpeg'])
                ) {
                    $doOptimize = true;
                }

                // Save media record to database
                $media = $mediaMapper->make();
                $media->filename = $fileUpload->getFilename();
                $media->caption = $this->getParsedBodyParam('caption');
                $media->width = ($fileUpload->width) ?: null;
                $media->height = ($fileUpload->height) ?: null;
                $media->feature = ($this->getParsedBodyParam('feature', false)) ? 'Y' : 'N';
                $media->mime_type = $fileUpload->mimeType;
                $media->optimized = ($doOptimize) ? $mediaMapper->getOptimizedCode('new') : $mediaMapper->getOptimizedCode('exclude');
                $mediaMapper->save($media);

                // Save category assignments
                $mediaCategoryMapMapper->saveMediaCategoryAssignments($media->id, $this->getParsedBodyParam('category'));

                // Optimize media uploads
                if ($doOptimize) {
                    $this->optimizeNewMedia();
                }
            } else {
                // Failed to upload, throw exception and return message to client
                throw new RuntimeException("File Upload Failed: " . $fileUpload->getErrorMessage());
            }

            // Clear file upload
            $fileUpload->clear('media-file');
        } catch (Throwable $th) {
            // Log exception
            $this->container->get('logger')->error('Media file upload failed: ' . $th->getTraceAsString());
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
        $mediaCategoryMapper = ($this->container->get('dataMapper'))('MediaCategoryMapper');
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
        $mediaCategoryMapper = ($this->container->get('dataMapper'))('MediaCategoryMapper');
        $categoriesPost = $this->getParsedBodyParam('category');

        foreach ($categoriesPost as $cat) {
            // Skip if category name is empty
            if (empty(trim($cat['name']))) {
                continue;
            }

            // Make category object and save
            $category = $mediaCategoryMapper->make();
            $category->id = (int) $cat['id'];
            $category->category = trim($cat['name']);
            $mediaCategoryMapper->save($category);
        }

        // Return to showing categories
        return $this->redirect('adminMediaCategoryEdit');
    }

    /**
     * Save Category Media Sort Order
     *
     * XHR Request
     * @param void
     * @return Response
     * @uses POST
     */
    public function saveCategoryMediaOrder(): Response
    {
        // Wrap in try catch to stop processing at any point and let the xhrResponse takeover
        try {
            $mediaCategoryMapMapper = ($this->container->get('dataMapper'))('MediaCategoryMapMapper');
            $categoryId = $this->getParsedBodyParam('categoryId');
            $mediaIds = $this->getParsedBodyParam('mediaIds');
            $status = "success";
            $text = "";

            if (is_numeric($categoryId) && $mediaIds) {
                $mediaArray = explode(',', $mediaIds);
                $mediaCategoryMapMapper->saveCategoryMediaAssignmentOrder((int) $categoryId, $mediaArray);
            }
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception saving media category assignments: ". $th->getMessage();
        }

        return $this->xhrResponse($status, $text);
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
            $mediaCategoryMapper = ($this->container->get('dataMapper'))('MediaCategoryMapper');
            $categoryId = $this->getParsedBodyParam('categoryId');
            $status = "success";
            $text = "";

            if (is_numeric($categoryId)) {
                // Delete category
                $category = $mediaCategoryMapper->make(['id' => $categoryId]);
                $mediaCategoryMapper->delete($category);

                // Foreign key constraints on media_category_map cascade delete to media associations
            }
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception deleting media category: ". $th->getMessage();
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
        // Submit background process to optimize media
        $script = escapeshellarg(ROOT_DIR . 'vendor/pitoncms/engine/cli/cli.php');
        $rootDir = escapeshellarg(ROOT_DIR);
        $logFile = ROOT_DIR . 'logs/optimize-media-' . date('Y-m-d') . '.log';

        exec("php $script optimize-media $rootDir >> $logFile 2>&1 &");
    }
}
