<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Controllers;

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
     */
    public function showPages()
    {
        // Get dependencies
        $page = ($this->container->dataMapper)('PageMapper');

        // Fetch pages & templates
        $data['pages'] = $page->findPages(true);
        $data['templates'] = $this->getPageTemplates('page');

        return $this->render('pages.html', $data);
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
        $collectionMapper = ($this->container->dataMapper)('CollectionMapper');
        $pageSettingMapper = ($this->container->dataMapper)('PageSettingMapper');
        $json = $this->container->json;

        // Fetch page, or create new page
        if (isset($args['id']) && is_numeric($args['id'])) {
            // Existing page
            $page = $pageMapper->findPageById($args['id']);
            $page->elements = $pageElementMapper->findElementsByPageId($args['id']);
            $page->settings = $pageSettingMapper->findPageSettings($args['id']);
        } else {
            // Get query params
            $definionParam = $this->request->getQueryParam('definition');
            $collectionIdParam = $this->request->getQueryParam('collection_id');

            // Validate that we have a proper definition file name
            if (null === $definionParam || 1 !== preg_match('/^[a-zA-Z0-9]+\.json$/', $definionParam)) {
                throw new Exception("PitonCMS: Invalid query parameter for 'definition': $definionParam");
            }

            // New page object
            $page = $pageMapper->make();
            $page->definition = $definionParam;

            // Is this request for a collection detail page?
            if (null !== $collectionIdParam) {
                if (1 !== preg_match('/^\d+$/', $collectionIdParam)) {
                    throw new Exception("PitonCMS: Invalid query parameter for 'collection_id': $collectionIdParam");
                }

                $collection = $collectionMapper->findById($collectionIdParam);
                $page->collection_id = $collectionIdParam;
                $page->collection_slug = $collection->slug;
            }
        }

        // Path to JSON definition file
        $jsonPath = ROOT_DIR . "structure/definitions/pages/{$page->definition}";
        if (null === $page->json = $json->getJson($jsonPath, 'page')) {
            $this->setAlert('danger', 'Page JSON Definition Error', $json->getErrorMessages());
        }

        // Merge saved page settnigs with settings from page JSON definition
        if (isset($page->json->settings)) {
            $page->settings = $this->mergeSettingsWithJsonSettings($page->settings, $page->json->settings, 'page');
        }

        return $this->render('editPage.html', $page);
    }

    /**
     * Save Page
     *
     * Create new page, or update existing page
     */
    public function savePage()
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $pageElementMapper = ($this->container->dataMapper)('PageElementMapper');
        $pageSettingMapper = ($this->container->dataMapper)('PageSettingMapper');
        $markdown = $this->container->markdownParser;
        $toolbox = $this->container->toolbox;
        $json = $this->container->json;

        // Create page object and populate POST data
        $page = $pageMapper->make();
        $page->id = $this->request->getParsedBodyParam('id');
        $page->collection_id = $this->request->getParsedBodyParam('collection_id');
        $page->definition = $this->request->getParsedBodyParam('definition');
        $page->template = $this->request->getParsedBodyParam('template');
        $page->title = $this->request->getParsedBodyParam('title');
        $page->sub_title = $this->request->getParsedBodyParam('sub_title');
        $page->slug = $toolbox->cleanUrl($this->request->getParsedBodyParam('slug'));
        $page->meta_description = $this->request->getParsedBodyParam('meta_description');
        $page->image_path = $this->request->getParsedBodyParam('image_path');

        // Process published date
        $publishedDate = $this->request->getParsedBodyParam('published_date');
        if (!empty($publishedDate)) {
        /*
        @link: http://php.net/strtotime
        Dates in the m/d/y or d-m-y formats are disambiguated by looking at the separator between the various
        components: if the separator is a slash (/), then the American m/d/y is assumed; whereas if the separator
        is a dash (-) or a dot (.), then the European d-m-y format is assumed.
        */
            $publishedDate = strtotime($publishedDate);
            $page->published_date = date('Y-m-d', $publishedDate);
        }

        // Save Page and get ID
        $page = $pageMapper->save($page);

        // Save any custom page settings
        if ($this->request->getParsedBodyParam('setting_id')) {
            foreach ($this->request->getParsedBodyParam('setting_id') as $settingKey => $settingValue) {
                $setting = $pageSettingMapper->make();
                $setting->id = $this->request->getParsedBodyParam('setting_id')[$settingKey];

                // Check for a page setting delete
                if (isset($this->request->getParsedBodyParam('setting_delete')[$settingKey])) {
                    $pageSettingMapper->delete($setting);
                    continue;
                }

                $setting->page_id = $page->id;
                $setting->setting_key = $this->request->getParsedBodyParam('setting_key')[$settingKey];
                $setting->setting_value = $this->request->getParsedBodyParam('setting_value')[$settingKey];

                $pageSettingMapper->save($setting);
            }
        }

        // Save page elements by block
        foreach ($this->request->getParsedBodyParam('block_key') as $key => $value) {
            // Save element
            $pageElement = $pageElementMapper->make();
            $pageElement->id = $this->request->getParsedBodyParam('element_id')[$key];
            $pageElement->page_id = $page->id;
            $pageElement->block_key = $this->request->getParsedBodyParam('block_key')[$key];
            $pageElement->definition = $this->request->getParsedBodyParam('element_type')[$key];
            $pageElement->element_sort = $this->request->getParsedBodyParam('element_sort')[$key];
            $pageElement->title = $this->request->getParsedBodyParam('element_title')[$key];
            $pageElement->content_raw = $this->request->getParsedBodyParam('content_raw')[$key];
            $pageElement->content = $markdown->text($this->request->getParsedBodyParam('content_raw')[$key]);
            $pageElement->excerpt = $toolbox->truncateHtmlText($pageElement->content, 60);
            $pageElement->collection_id = $this->request->getParsedBodyParam('element_collection_id')[$key];
            $pageElement->gallery_id = $this->request->getParsedBodyParam('gallery_id')[$key];
            $pageElement->embedded = $this->request->getParsedBodyParam('embedded')[$key];

            // If the image path does not start with http then get base name
            $imagePath = $this->request->getParsedBodyParam('element_image_path')[$key];
            if (stripos($imagePath, 'http') === 0) {
                $pageElement->image_path = $imagePath;
            } else {
                $pageElement->image_path = pathinfo($imagePath, PATHINFO_BASENAME);
            }

            // Get the elementTemplateFile from element JSON file
            $jsonPath = ROOT_DIR . "structure/definitions/elements/{$pageElement->definition}";

            if (null === $elementDefinition = $json->getJson($jsonPath, 'element')) {
                throw new Exception('Element JSON Definition Error: ' . print_r($json->getErrorMessages(), true));
            }

            $pageElement->template = $elementDefinition->elementTemplateFile;

            $pageElement = $pageElementMapper->save($pageElement);
        }

        // Determine redirect path based on whether this is a collection
        if (!empty($this->request->getParsedBodyParam('collection_id'))) {
            $redirectPath = 'adminCollections';
        } else {
            $redirectPath = 'adminPages';
        }

        // Redirect back to show page
        return $this->redirect($redirectPath);
    }

    /**
     * Delete Page
     *
     * Home page is not restricted from being deleted
     */
    public function deletePage()
    {
        // Get dependencies
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $pageElementMapper = ($this->container->dataMapper)('PageElementMapper');
        $pageSettingMapper = ($this->container->dataMapper)('PageSettingMapper');

        if (null !== $pageId = $this->request->getParsedBodyParam('id')) {
            // Delete page
            $page = $pageMapper->make();
            $page->id = $pageId;
            $pageMapper->delete($page);

            // Delete page elements & page settings
            $pageElementMapper->deleteElementsByPageId($pageId);
            $pageSettingMapper->deleteByPageId($pageId);
        }

        // Redirect back to show pages
        return $this->redirect('adminPages');
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

        $template = '{% import "@admin/editElementMacro.html" as form %}';
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
