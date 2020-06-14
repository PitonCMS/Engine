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

use Piton\Models\Entities\PitonEntity;
use Slim\Http\Response;
use Exception;
use DateTime;
use Throwable;

/**
 * Piton Admin Page and Collection Controller
 */
class AdminPageController extends AdminBaseController
{
    /**
     * Show Pages and Collection Pages
     *
     * Show all pages and collection pages
     * @param array $args Route arguments
     * @return Response
     */
    public function showPages(): Response
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $pagination = $this->container->adminPagePagination;
        $definition = $this->container->jsonDefinitionHandler;
        $pageTemplates = array_merge($definition->getPages(), $definition->getCollections());

        // Get filter if requested
        $status = htmlspecialchars($this->request->getQueryParam('pageStatus', 'all'));

        // Validate filter string
        if (!in_array($status, ['published', 'pending', 'draft', 'all'])) {
            $this->setAlert('danger', 'Invalid Value', "The filter query $status is not valid.");
        }

        // Get data
        $data['pages'] = $pageMapper->findContent($status, $pagination->getLimit(), $pagination->getOffset()) ?? [];

        // Setup pagination
        $pagination->setPagePath($this->container->router->pathFor('adminPage'));
        $pagination->setTotalResultsFound($pageMapper->foundRows() ?? 0);
        $this->container->view->addExtension($pagination);

        // Use filename as key for quick look up when adding template name into result set
        $pageTemplates = array_combine(array_column($pageTemplates, 'filename'), $pageTemplates);

        // Set template name in result set
        foreach ($data['pages'] as &$page) {
            $page->template_name = $pageTemplates[$page->template]['name'] ?? null;
        }

        // Check if this request was XHR
        if ($this->request->isXhr()) {
            try {
                // Render template
                $template =
                    "{% import \"@admin/pages/_pageMacros.html\" as pageMacro %}
                    {% for p in page.pages %}
                        {{ pageMacro.pageListItem(p, 'adminPageEdit') }}
                    {% endfor %}";

                $status = "success";
                $text = $this->container->view->fetchFromString($template, ['page' => $data]);
            } catch (Throwable $th) {
                $status = "error";
                $text = "Exception getting data: {$th->getMessage()}";
            }

            return $this->xhrResponse($status, $text);
        }

        // Otherwise render whole page
        return $this->render('pages/pages.html', $data);
    }

    /**
     * Edit Page or Collection Page
     *
     * Create new page, or edit existing page
     * @param array $args
     * @return Response
     */
    public function editPage($args): Response
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $pageElementMapper = ($this->container->dataMapper)('PageElementMapper');
        $dataStoreMapper = ($this->container->dataMapper)('DataStoreMapper');
        $collectionMapper = ($this->container->dataMapper)('CollectionMapper');
        $definition = $this->container->jsonDefinitionHandler;

        // Fetch page, or create new page
        if (isset($args['id']) && is_numeric($args['id'])) {
            // Load existing page from database.
            $page = $pageMapper->findById((int) $args['id']);
            $page->setBlockElements($pageElementMapper->findElementsByPageId($page->id));
            $settings = $dataStoreMapper->findPageSettings($page->id) ?? [];
        } else {
            // New page object
            $page = $pageMapper->make();
            $settings = [];

            // Create new page, and get template from query string
            $templateParam = $this->request->getQueryParam('definition');
            if ($templateParam) {
                $templateParam = htmlspecialchars($templateParam);

                // Validate that we have a proper definition file name
                if (null === $templateParam || 1 !== preg_match('/^[a-zA-Z0-9\/]+$/', $templateParam)) {
                    // $this->setAlert('danger', 'Invalid Template Name', 'The template name must only include a-z, A-Z, 0-9, and /');
                    throw new Exception("PitonCMS: Invalid query parameter for 'definition': $templateParam");
                }

                $page->template = $templateParam;
            }

            // Get collection details for collection pages. (Collection details for existing pages are returned with the findById() query above.)
            $collectionId = $this->request->getQueryParam('collectionId');
            if (is_numeric($collectionId)) {
                $collection = $collectionMapper->findById((int) $collectionId);
                $page->collection_id = $collectionId;
                $page->collection_title = $collection->collection_title;
                $page->collection_slug = $collection->collection_slug;
                $page->template = $collection->collection_definition;
            }
        }

        // Get page definition
        if (null === $page->definition = $definition->getPage($page->template . '.json')) {
            $this->setAlert('danger', 'Page JSON Definition Error', $definition->getErrorMessages());
        }

        // If this is a new page (has no ID) then add a default element to each block
        if (empty($page->id)) {
            foreach ($page->definition->blocks as $block) {
                $newElement = $pageElementMapper->make();
                $newElement->block_key = $block->key;
                $newElement->template = $block->elementTypeDefault;

                $page->setBlockElements([$newElement]);
            }
        }

        // Merge saved page settings with settings from page JSON definition
        if (isset($page->definition->settings)) {
            $page->settings = $this->mergeSettings($settings, $page->definition->settings);
        }

        return $this->render('pages/pageEdit.html', $page);
    }

    /**
     * Save Page
     *
     * Create or update page wrapper
     */
    public function savePage()
    {
        // Save settings and elements
        $pageEntity = $this->savePageHeader();
        $this->savePageSettings($pageEntity->id);
        $this->savePageElements($pageEntity->id);

        // Determine redirect path based on whether this is a collection detail page
        if (!empty($this->request->getParsedBodyParam('collection_slug'))) {
            return $this->redirect('adminCollection', ['collectionSlug' => $this->request->getParsedBodyParam('collection_slug')]);
        } else {
            return $this->redirect('adminPage');
        }
    }

    /**
     * Save Page
     *
     * Create new page, or update existing page
     * From $_POST array
     * @param void
     * @return PitonEntity
     */
    public function savePageHeader(): ?PitonEntity
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $toolbox = $this->container->toolbox;

        // Get page object
        $pageId = empty($this->request->getParsedBodyParam('page_id')) ? null : (int) $this->request->getParsedBodyParam('page_id');
        $newSlug = $toolbox->cleanUrl($this->request->getParsedBodyParam('page_slug'));

        // Try to get the original page from database for update
        if (null !== $pageId) {
            if (null === $page = $pageMapper->findById($pageId)) {
                throw new Exception("PitonCMS: savePageHeader Page $pageId not found.");
            }

            // Ensure we are not futzing with the home page slug
            if ($page->page_slug === 'home' && $newSlug !== 'home') {
                throw new Exception('PitonCMS: Cannot change home page slug.');
            }
        } else {
            $page = $pageMapper->make();
        }

        $page->collection_id = $this->request->getParsedBodyParam('collection_id');
        $page->template = $this->request->getParsedBodyParam('template');
        $page->title = $this->request->getParsedBodyParam('title');
        $page->sub_title = $this->request->getParsedBodyParam('sub_title');
        $page->page_slug = $newSlug;
        $page->meta_description = $this->request->getParsedBodyParam('meta_description');
        $page->media_id = $this->request->getParsedBodyParam('page_media_id');

        // Process published date
        // The leading ! sets the time to 00:00:00
        $phpDateFormat = [
            'mm/dd/yyyy' => '!m/d/Y',
            'dd-mm-yyyy' => '!d-m-Y',
            'dd.mm.yyyy' => '!d.m.Y'
        ];
        $publishedDate = $this->request->getParsedBodyParam('published_date');
        $publishNow = $this->request->getParsedBodyParam('publish_now');

        // Set publish date
        if (empty($publishedDate) && isset($publishNow)) {
            // Set publish date to today if date is not set and the publish now button was used
            $date = new DateTime();
            $page->published_date = $date->format('Y-m-d');
        } elseif (!empty($publishedDate)) {
            // Otherwise if a date was provided, honor that date
            $date = DateTime::createFromFormat($phpDateFormat[$this->siteSettings['dateFormat']], $publishedDate);
            $page->published_date = $date->format('Y-m-d');
        } else {
            $page->published_date = null;
        }

        // Save Page and return ID
        return $pageMapper->save($page);
    }

    /**
     * Save Page Settings
     *
     * From $_POST array
     * @param int $pageId
     * @return void
     */
    protected function savePageSettings(int $pageId)
    {
        // Save any custom page settings
        if ($post = $this->request->getParsedBodyParam('setting')) {
            // Get dependencies
            $dataStoreMapper = ($this->container->dataMapper)('DataStoreMapper');

            foreach ($post as $row) {
                $setting = $dataStoreMapper->make();
                $setting->id = $row['id'];

                // Check for a page setting delete
                if (isset($row['delete'])) {
                    $dataStoreMapper->delete($setting);
                    continue;
                }

                $setting->page_id = $pageId;
                $setting->category = 'page';
                $setting->setting_key = $row['setting_key'];
                $setting->setting_value = $row['setting_value'];
                $dataStoreMapper->save($setting);
            }
        }
    }

    /**
     * Save Page Elements
     *
     * From $_POST array
     * @param int $pageId
     * @return void
     */
    protected function savePageElements(int $pageId)
    {
        $pageElementMapper = ($this->container->dataMapper)('PageElementMapper');
        $markdown = $this->container->markdownParser;
        $toolbox = $this->container->toolbox;

        // Save page elements by block
        $index = 1;
        foreach ($this->request->getParsedBodyParam('block_key') as $key => $value) {
            // Save element
            $pageElement = $pageElementMapper->make();
            $pageElement->id = $this->request->getParsedBodyParam('element_id')[$key];
            $pageElement->page_id = $pageId;
            $pageElement->block_key = $this->request->getParsedBodyParam('block_key')[$key];
            $pageElement->template = $this->request->getParsedBodyParam('element_template')[$key];
            $pageElement->element_sort = $index++;
            $pageElement->title = $this->request->getParsedBodyParam('element_title')[$key];
            $pageElement->content_raw = $this->request->getParsedBodyParam('content_raw')[$key];
            $pageElement->content = $markdown->text($this->request->getParsedBodyParam('content_raw')[$key]);
            $pageElement->excerpt = $toolbox->truncateHtmlText($pageElement->content, 60);
            $pageElement->collection_id = $this->request->getParsedBodyParam('element_collection_id')[$key];
            $pageElement->gallery_id = $this->request->getParsedBodyParam('element_gallery_id')[$key];
            $pageElement->embedded = $this->request->getParsedBodyParam('embedded')[$key];
            $pageElement->media_id = $this->request->getParsedBodyParam('element_media_id')[$key];

            $pageElement = $pageElementMapper->save($pageElement);
        }
    }

    /**
     * Delete Page
     *
     * Home page is restricted from being deleted
     */
    public function deletePage()
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');

        $pageId = empty($this->request->getParsedBodyParam('page_id')) ? null : $this->request->getParsedBodyParam('page_id');

        if (null !== $pageId) {
            // Ensure this is not the home page
            $page = $pageMapper->findById((int) $pageId);

            if ($page->page_slug === 'home') {
                throw new Exception('PitonCMS: Cannot delete home page');
            }

            // Delete page. Elements, Settings, and Navigation records are deleted by foreign key database constraints
            $pageMapper->delete($page);
        }

        // Redirect back to show pages
        return $this->redirect('adminPage');
    }

    /**
     * Get New Element Form
     *
     * XHR Request
     * Renders new element form with initial values and returns XHR request
     * @param void
     * @uses queryParam pageTemplate
     * @uses queryParam blockKey
     * @return Response
     */
    public function getElement(): Response
    {
        // Wrap in try catch to stop processing at any point and let the xhrResponse takeover
        try {
            // Get dependencies
            $definition = $this->container->jsonDefinitionHandler;

            $pageTemplate = htmlspecialchars($this->request->getQueryParam('pageTemplate'));
            $blockKey = htmlspecialchars($this->request->getQueryParam('blockKey'));

            // Get page definition
            if (null === $pageDefinition = $definition->getPage($pageTemplate . '.json')) {
                throw new Exception('Page Definition Error', print_r($definition->getErrorMessages(), true));
            }

            // Get defined blocks and use block key as array index
            $blocks = array_combine(array_column($pageDefinition->blocks, 'key'), $pageDefinition->blocks);

            $form['blockKey'] = $blockKey;
            $form['elementTypeOptions'] = $blocks[$blockKey]->elementTypeOptions ?? null;

            // Make string template
            $template = '{% import "@admin/pages/_pageMacros.html" as pageMacro %}';
            $template .= ' {{ pageMacro.elementForm(element, element.blockKey, element.elementTypeOptions) }}';

            $status = "success";
            $text = $this->container->view->fetchFromString($template, ['element' => $form]);
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception getting new element: ". $th->getMessage();
        }

        return $this->xhrResponse($status, $text);
    }

    /**
     * Delete Element
     *
     * XHR Request
     * Deletes element returns XHR request
     * @param void
     * @uses POST
     * @uses elementId Element ID
     * @return Response
     */
    public function deleteElement()
    {
        // Get dependencies
        $pageElement = ($this->container->dataMapper)('PageElementMapper');

        // Wrap in try catch to stop processing at any point and let the xhrResponse takeover
        try {
            // Check that we received an ID
            $id = htmlspecialchars($this->request->getParsedBodyParam('elementId', 'x'));
            if (!is_numeric($id)) {
                throw new Exception("Invalid element ID");
            }

            // Delete block element
            $blockElement = $pageElement->make();
            $blockElement->id = (int) $id;
            $pageElement->delete($blockElement);

            $status = 'success';
            $text = null;
        } catch (Throwable $th) {
            $status = "error";
            $text = "Exception getting new element: ". $th->getMessage();
        }

        return $this->xhrResponse($status, $text);
    }

    /**
     * Show Collection Groups
     *
     * @param void
     * @return Response
     */
    public function showCollectionGroups()
    {
        $collectionMapper = ($this->container->dataMapper)('CollectionMapper');

        $collections = $collectionMapper->find();

        return $this->render('pages/collections.html', ['collections' => $collections]);
    }

    /**
     * Edit Collection Group
     *
     * Create, edit, or delete collection group
     * @param array $args
     * @return Response
     */
    public function editCollection($args): Response
    {
        // Get dependencies
        $collectionMapper = ($this->container->dataMapper)('CollectionMapper');
        $definition = $this->container->jsonDefinitionHandler;

        if (isset($args['id']) && is_numeric($args['id'])) {
            // If a collection ID was provided, load that collection
            $data['collection'] = $collectionMapper->findById((int) $args['id']);
        } else {
            // Create new collection object
            $data['collection'] = $collectionMapper->make();
        }

        // Get available collection templates
        $data['templates'] = $definition->getCollections();

        return $this->render('pages/collectionEdit.html', $data);
    }

    /**
     * Save Collection Group
     *
     * Create new collection, or update existing collection
     * From $_POST array
     * @param void
     * @return Response
     */
    public function saveCollection(): Response
    {
        // Get dependencies
        $collectionMapper = ($this->container->dataMapper)('CollectionMapper');
        $toolbox = $this->container->toolbox;

        // Is there an ID?
        $collectionId = empty($this->request->getParsedBodyParam('collection_id')) ? null : (int) $this->request->getParsedBodyParam('collection_id');

        // Try to get the original collection group from database for update
        if (null !== $collectionId) {
            if (null === $collection = $collectionMapper->findById($collectionId)) {
                throw new Exception("PitonCMS: saveCollection Collection $collectionId not found.");
            }
        } else {
            $collection = $collectionMapper->make();
        }

        $collection->id = $collectionId;
        $collection->collection_title = $this->request->getParsedBodyParam('collection_title');
        $collection->collection_slug = $toolbox->cleanUrl($this->request->getParsedBodyParam('collection_slug'));

        // The definition is provided only if no pages are assigned in the form
        if ($this->request->getParsedBodyParam('collection_definition', null) !== null) {
            $collection->collection_definition = $this->request->getParsedBodyParam('collection_definition');
        }

        $collectionMapper->save($collection);

        // Save collection and redirect to collection pages
        return $this->redirect('adminCollection', ['collectionSlug' => $collection->collection_slug]);
    }

    /**
     * Delete Collection Group
     *
     * Collections with assigned pages are restricted from being deleted
     */
    public function deleteCollection()
    {
        // Get dependencies
        $collectionMapper = ($this->container->dataMapper)('CollectionMapper');

        // Get collection to delete
        $collectionId = $this->request->getParsedBodyParam('collection_id');
        $collection = $collectionMapper->findById((int) $collectionId);

        // Integrity checks before deleting
        if (empty($collection)) {
            $collectionId = $collectionId ?? 'null';
            throw new Exception("PitonCMS: Collection ID $collectionId not found for deletion");
        }

        if ($collection->page_count > 0) {
            throw new Exception("PitonCMS: Cannot delete a Collection with pages assigned. First remove detail pages from this collection");
        }

        $collectionMapper->delete($collection);

        // Redirect back to show pages
        return $this->redirect('adminCollection');
    }
}
