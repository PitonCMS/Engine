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

use Slim\Http\Response;
use Exception;

/**
 * Piton Admin Page Controller
 */
class AdminPageController extends AdminBaseController
{
    /**
     * Show Pages
     *
     * Show all pages and page templates
     * @param array $args Route arguments
     */
    public function showPages($args)
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $definition = $this->container->definition;

        // Fetch pages & templates
        if (isset($args['type']) && $args['type'] === 'collection') {
            $data['pages'] = $pageMapper->findCollectionPages(true);
            $data['templates'] = $definition->getCollections();
            $data['type'] = 'collection';

            // Get distinct list of collection names for section separators
            $data['collectionNames'] = [];
            foreach ($data['pages'] as $page) {
                $data['collectionNames'][$page->collection_slug] = [
                    'value' => $page->collection_slug,
                    'name' => ucfirst($page->collection_slug)
                ];
            }
        } else {
            $data['pages'] = $pageMapper->findPages(true);
            $data['templates'] = $definition->getPages();
            $data['type'] = 'page';
        }

        return $this->render('pages/pages.html', $data);
    }

    /**
     * Edit Page
     *
     * Create new page, or edit existing page
     */
    public function editPage($args)
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $pageElementMapper = ($this->container->dataMapper)('PageElementMapper');
        $settingMapper = ($this->container->dataMapper)('SettingMapper');
        $definition = $this->container->definition;

        // Fetch page, or create new page
        if (isset($args['id']) && is_numeric($args['id'])) {
            // Load existing page from database
            $page = $pageMapper->findById((int) $args['id']);
            $page->elements = $pageElementMapper->findElementsByPageId((int) $args['id']);
            $page->settings = $settingMapper->findPageSettings((int) $args['id']);
        } else {
            // Create new page, and get template from query string
            $definionParam = $this->request->getQueryParam('definition');

            // Validate that we have a proper definition file name
            if (null === $definionParam || 1 !== preg_match('/^[a-zA-Z0-9]+\.json$/', $definionParam)) {
                throw new Exception("PitonCMS: Invalid query parameter for 'definition': $definionParam");
            }

            // New page object
            $page = $pageMapper->make();
            $page->definition = $definionParam;
        }

        // Get page definition
        if (null === $page->json = $definition->getPage($page->definition)) {
            $this->setAlert('danger', 'Page JSON Definition Error', $definition->getErrorMessages());
        }

        // Merge saved page settings with settings from page JSON definition
        if (isset($page->json->settings)) {
            $page->settings = $this->mergeSettings($page->settings, $page->json->settings);
        }

        // Set template type: collection|page
        $page->type = ($page->json->templateType === 'collection') ? 'collection' : 'page';

        return $this->render('pages/editPage.html', $page);
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

        // Determine redirect path based on whether this is a collection page
        if (!empty($this->request->getParsedBodyParam('collection_slug'))) {
            $redirectRoute = 'adminCollections';
        } else {
            $redirectRoute = 'adminPages';
        }

        // Redirect back to show page
        return $this->redirect($redirectRoute);
    }

    /**
     * Save Page
     *
     * Create new page, or update existing page
     * From $_POST array
     */
    public function savePageHeader()
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $toolbox = $this->container->toolbox;

        // Get page object
        $pageId = empty($this->request->getParsedBodyParam('id')) ? null : $this->request->getParsedBodyParam('id');
        $newSlug = $toolbox->cleanUrl($this->request->getParsedBodyParam('page_slug'));

        // Get the original page from database, if exists, for update
        if (null !== $pageId) {
            $page = $pageMapper->findById($pageId);

            // Ensure we are not futzing with the home page slug
            if ($page->page_slug === 'home' && $newSlug !== 'home') {
                throw new Exception('PitonCMS: Cannot change home page slug');
            }
        } else {
            $page = $pageMapper->make();
        }

        $page->collection_slug = $this->request->getParsedBodyParam('collection_slug');
        $page->definition = $this->request->getParsedBodyParam('definition');
        $page->template = $this->request->getParsedBodyParam('template');
        $page->title = $this->request->getParsedBodyParam('title');
        $page->sub_title = $this->request->getParsedBodyParam('sub_title');
        $page->page_slug = $newSlug;
        $page->meta_description = $this->request->getParsedBodyParam('meta_description');
        $page->image_path = $this->request->getParsedBodyParam('image_path');

        // Process published date
        $publishedDate = $this->request->getParsedBodyParam('published_date');
        if (!empty($publishedDate)) {
            // The leading ! sets the time to 00:00:00
            $phpDateFormat = [
                'mm/dd/yyyy' => '!m/d/Y',
                'dd-mm-yyyy' => '!d-m-Y',
                'dd.mm.yyyy' => '!d.m.Y'
            ];

            $date = \DateTime::createFromFormat($phpDateFormat[$this->siteSettings['dateFormat']], $publishedDate);
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
            $settingMapper = ($this->container->dataMapper)('SettingMapper');

            foreach ($post as $row) {
                $setting = $settingMapper->make();
                $setting->id = $row['id'];

                // Check for a page setting delete
                if (isset($row['delete'])) {
                    $settingMapper->delete($setting);
                    continue;
                }

                $setting->reference_id = $pageId;
                $setting->category = 'page';
                $setting->setting_key = $row['setting_key'];
                $setting->setting_value = $row['setting_value'];
                $settingMapper->save($setting);
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
        $definition = $this->container->definition;

        // Save page elements by block
        foreach ($this->request->getParsedBodyParam('block_key') as $key => $value) {
            // Save element
            $pageElement = $pageElementMapper->make();
            $pageElement->id = $this->request->getParsedBodyParam('element_id')[$key];
            $pageElement->page_id = $pageId;
            $pageElement->block_key = $this->request->getParsedBodyParam('block_key')[$key];
            $pageElement->definition = $this->request->getParsedBodyParam('element_type')[$key];
            $pageElement->element_sort = $this->request->getParsedBodyParam('element_sort')[$key];
            $pageElement->title = $this->request->getParsedBodyParam('element_title')[$key];
            $pageElement->content_raw = $this->request->getParsedBodyParam('content_raw')[$key];
            $pageElement->content = $markdown->text($this->request->getParsedBodyParam('content_raw')[$key]);
            $pageElement->excerpt = $toolbox->truncateHtmlText($pageElement->content, 60);
            $pageElement->collection_slug = $this->request->getParsedBodyParam('element_collection_slug')[$key];
            $pageElement->gallery_id = $this->request->getParsedBodyParam('element_gallery_id')[$key];
            $pageElement->embedded = $this->request->getParsedBodyParam('embedded')[$key];

            // If the image path starts with http then save as-is
            $imagePath = $this->request->getParsedBodyParam('element_image_path')[$key];
            if (mb_stripos($imagePath, 'http') === 0) {
                $pageElement->image_path = $imagePath;
            } else {
                // Else get the name name to save
                $pageElement->image_path = pathinfo($imagePath, PATHINFO_BASENAME);
            }

            // Get the elementTemplateFile from element JSON file
            if (null === $elementDefinition = $definition->getElement($pageElement->definition)) {
                throw new Exception('Element JSON Definition Error: ' . print_r($definition->getErrorMessages(), true));
            }

            $pageElement->template = $elementDefinition->elementTemplateFile;

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
        $pageElementMapper = ($this->container->dataMapper)('PageElementMapper');
        $settingMapper = ($this->container->dataMapper)('SettingMapper');
        $navigationMapper = ($this->container->dataMapper)('NavigationMapper');

        $pageId = empty($this->request->getParsedBodyParam('id')) ? null : $this->request->getParsedBodyParam('id');

        if (null !== $pageId) {
            // Ensure this is not the home page
            $page = $pageMapper->findById($pageId);

            if ($page->page_slug === 'home') {
                throw new Exception('PitonCMS: Cannot delete home page');
            }

            // Delete page
            $pageMapper->delete($page);

            // Delete page elements, page settings, and navigation entry
            $pageElementMapper->deleteElementsByPageId($pageId);
            $settingMapper->deleteByPageId($pageId);
            $navigationMapper->deleteByPageId($pageId);
        }

        // Determine redirect path based on whether this was a collection page
        if (!empty($this->request->getParsedBodyParam('collection_slug'))) {
            $redirectRoute = 'adminCollections';
        } else {
            $redirectRoute = 'adminPages';
        }

        // Redirect back to show pages
        return $this->redirect($redirectRoute);
    }

    /**
     * New Element Form
     *
     * Renders new element form with initial values, and returns via Ajax to browser.
     * At a minimum, the element form is expecting these values:
     * - blockKey
     * - elementTypeDefault
     * - elementSort
     * - elementTypeOptions | optional, comma separated list of approved element types
     */
    public function newElementForm()
    {
        $parsedBody = $this->request->getParsedBody();

        $form['block_key'] = $parsedBody['blockKey'];
        $form['definition'] = $parsedBody['elementType'];
        $form['element_sort'] = 1;

        // Only include element type options if the string is not empty
        if (!empty($parsedBody['elementTypeOptions'])) {
            $form['elementTypeOptions'] = explode(',', $parsedBody['elementTypeOptions']);
        }

        $template = '{% import "@admin/pages/_editElementMacro.html" as form %}';
        $template .= ' {{ form.elementForm(element, element.block_key, element.elementTypeOptions) }}';
        $elementFormHtml = $this->container->view->fetchFromString($template, ['element' => $form]);

        // Set the response type
        $r = $this->response->withHeader('Content-Type', 'application/json');

        return $r->write(json_encode(["html" => $elementFormHtml]));
    }

    /**
     * Delete Element
     *
     * XHR request
     */
    public function deleteElement()
    {
        // Get dependencies
        $PageElement = ($this->container->dataMapper)('PageElementMapper');

        // Check that we received an ID
        if ($this->request->getParsedBodyParam('id')) {
            // Delete block element
            $blockElement = $PageElement->make();
            $blockElement->id = $this->request->getParsedBodyParam('id');
            $PageElement->delete($blockElement);

            $status = 'success';
        } else {
            $status = 'error';
        }

        // Set the response type
        $r = $this->response->withHeader('Content-Type', 'application/json');

        return $r->write(json_encode(['status' => $status]));
    }
}
