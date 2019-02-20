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
     * Choose Page Template
     *
     * Select page template for new page
     */
    public function chooseTemplate()
    {
        $toolbox = $this->container->toolbox;
        $json = $this->container->json;

        $jsonPath = ROOT_DIR . "themes/{$this->siteSettings['theme']}/definitions/pages/";
        $templates = [];

        foreach ($toolbox->getDirectoryFiles($jsonPath) as $row) {
            // Get definition files and filter out collection types
            if (null === $definition = $json->getJson($jsonPath . $row['filename'], 'page')) {
                $this->setAlert('danger', 'Page JSON Definition Error', $json->getErrorMessages());
                break;
            }

            if ($definition->templateType === 'collection') {
                continue;
            }

            $templates[] = [
                'filename' => $row['filename'],
                'name' => $definition->templateName,
                'description' => $definition->templateDescription
            ];
        }

        return $this->render('choosePageTemplate.html', ['templates' => $templates]);
    }

    /**
     * Show Pages
     *
     * Show all pages with blocks and elements
     */
    public function showPages()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $Page = $mapper('PageMapper');
        $PageElement = $mapper('PageElementMapper');

        // Fetch pages
        $pages = $Page->findPages(true);

        return $this->render('pages.html', $pages);
    }

    /**
     * Edit Page
     *
     * Create new page, or edit existing page
     */
    public function editPage($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $pageMapper = $mapper('PageMapper');
        $pageElementMapper = $mapper('PageElementMapper');
        $collectionMapper = $mapper('CollectionMapper');
        $pageSettingMapper = $mapper('PageSettingMapper');
        $json = $this->container->json;

        // Fetch page, or create new page
        if (is_numeric($args['id'])) {
            $page = $pageMapper->findById($args['id']);
            $page->elements = $pageElementMapper->findElementsByPageId($args['id']);
            $page->settings = $pageSettingMapper->findPageSettings($args['id']);
        } elseif (is_string($args['id'])) {
            // New page object
            $page = $pageMapper->make();
            $page->definition = $args['id'];
        }

        // If this page is for a collection, get collection record
        if (isset($args['collection'])) {
            $page->collection = $collectionMapper->findById($args['collection']);
        }

        // Path to JSON definition file
        $jsonPath = ROOT_DIR . "themes/{$this->siteSettings['theme']}/definitions/pages/{$page->definition}";
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
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');
        $PageElementMapper = $mapper('PageElementMapper');
        $CollectionMapper = $mapper('CollectionMapper');
        $PageSettingMapper = $mapper('PageSettingMapper');
        $Markdown = $this->container->markdownParser;
        $toolbox = $this->container->toolbox;
        $json = $this->container->json;

        // Create page object and populate POST data
        $page = $PageMapper->make();
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
        $page->published_date = ($this->request->getParsedBodyParam('published_date')) ?: '';
        if (!empty($page->published_date)) {
        /*
        @link: http://php.net/strtotime
        Dates in the m/d/y or d-m-y formats are disambiguated by looking at the separator between the various
        components: if the separator is a slash (/), then the American m/d/y is assumed; whereas if the separator
        is a dash (-) or a dot (.), then the European d-m-y format is assumed.
        */
            $publishedDate = strtotime($page->published_date);
            $page->published_date = date('Y-m-d', $publishedDate);
        }

        // Save Page and get ID
        $page = $PageMapper->save($page);

        // Save any custom field setting inputs
        if ($this->request->getParsedBodyParam('setting_id')) {
            foreach ($this->request->getParsedBodyParam('setting_id') as $fieldKey => $field) {
                $setting = $PageSettingMapper->make();
                $setting->id = $this->request->getParsedBodyParam('setting_id')[$fieldKey];
                $setting->page_id = $page->id;
                $setting->scope = 'page';
                $setting->category = 'page';
                $setting->setting_key = $this->request->getParsedBodyParam('setting_key')[$fieldKey];
                $setting->setting_value = $this->request->getParsedBodyParam('setting_value')[$fieldKey];

                $PageSettingMapper->save($setting);
            }
        }

        // Save page block elements
        foreach ($this->request->getParsedBodyParam('block_key') as $key => $value) {
            // Save element
            $pageElement = $PageElementMapper->make();
            $pageElement->id = $this->request->getParsedBodyParam('element_id')[$key];
            $pageElement->page_id = $page->id;
            $pageElement->block_key = $this->request->getParsedBodyParam('block_key')[$key];
            $pageElement->template = $this->request->getParsedBodyParam('element_type')[$key] . '.html';
            $pageElement->element_type = $this->request->getParsedBodyParam('element_type')[$key];
            $pageElement->element_sort = $this->request->getParsedBodyParam('element_sort')[$key];
            $pageElement->title = $this->request->getParsedBodyParam('element_title')[$key];
            $pageElement->content_raw = $this->request->getParsedBodyParam('content_raw')[$key];
            $pageElement->content = $Markdown->text($this->request->getParsedBodyParam('content_raw')[$key]);
            $pageElement->excerpt = $toolbox->truncateHtmlText($pageElement->content, 60);
            $pageElement->collection_id = $this->request->getParsedBodyParam('element_collection_id')[$key];
            $pageElement->gallery_id = $this->request->getParsedBodyParam('gallery_id')[$key];
            $pageElement->image_path = $this->request->getParsedBodyParam('element_image_path')[$key];
            $pageElement->video_path = $this->request->getParsedBodyParam('video_path')[$key];

            // If a custom element type was selected, get the JSON definition file
            if (preg_match('/.+\.json$/', ($this->request->getParsedBodyParam('element_type')[$key]))) {
                $customType = $this->request->getParsedBodyParam('element_type')[$key];

                // Get element definition file
                $jsonPath = ROOT_DIR . "themes/{$this->siteSettings['theme']}/definitions/elements/{$customType}";

                if (null === $definition = $json->getJson($jsonPath, 'element')) {
                    throw new Exception('Element definition error: ' . print_r($json->getErrorMessages(), true));
                }

                $pageElement->template = $definition->elementTemplateFile;
            }

            $pageElement = $PageElementMapper->save($pageElement);
        }

        // Determine redirect based on whether this is a collection
        if (!empty($this->request->getParsedBodyParam('collection_id'))) {
            $redirectPath = 'showCollections';
        } else {
            $redirectPath = 'showPages';
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
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');
        $PageElementMapper = $mapper('PageElementMapper');
        $PageSettingMapper = $mapper('PageSettingMapper');

        if ($this->request->getParsedBodyParam('button') === 'delete' && $this->request->getParsedBodyParam('id')) {
            // Delete page
            $page = $PageMapper->make();
            $page->id = $this->request->getParsedBodyParam('id');
            $PageMapper->delete($page);

            // Delete page elements & page settings
            $PageElementMapper->deleteElementsByPageId($this->request->getParsedBodyParam('id'));
            $PageSettingMapper->deleteByPageId($this->request->getParsedBodyParam('id'));
        } else {
            throw new Exception('Invalid page delete request.');
        }

        // Redirect back to show pages
        return $this->redirect('showPages');
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
        $form['element_type'] = $parsedBody['elementType'];
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
        $mapper = $this->container->dataMapper;
        $PageElement = $mapper('PageElementMapper');

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
