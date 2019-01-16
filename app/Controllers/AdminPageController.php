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
     * Choose Page Layout
     *
     * Select a page layout to use
     */
    public function chooseLayout()
    {
        $toolbox = $this->container->toolbox;
        $layouts = [];

        $theme = $this->container->get('settings')['site']['theme'];
        $jsonPath = ROOT_DIR . "themes/{$theme}/definitions";
        $layoutFiles = $toolbox->getDirectoryFiles($jsonPath);

        foreach ($layoutFiles as $row) {
            $layouts[$row['basename']] = $row['readname'];
        }

        return $this->render('choosePageLayout.html', ['layouts' => $layouts]);
    }

    /**
     * Show Pages
     *
     * Show pages with blocks and elements
     */
    public function showPages()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $Page = $mapper('PageMapper');
        $PageElement = $mapper('PageElementMapper');

        // Fetch pages
        $pages = $Page->findPages(false);

        // Fetch all block elements for each page
        if ($pages) {
            foreach ($pages as $key => $row) {
                $pages[$key]->blocks = $this->buildElementsByBlock($PageElement->findElementsByPageId($row->id));
            }
        }

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
        $json = $this->container->json;
        $theme = $this->container->get('settings')['site']['theme'];

        // Fetch page, or create new page
        if (is_numeric($args['id'])) {
            $page = $pageMapper->findById($args['id']);
            $page->elements = $pageElementMapper->findElementsByPageId($args['id']);
        } elseif (is_string($args['id'])) {
            // New page
            $page = $pageMapper->make();
            $page->template = $args['id'] . '.html';
        }

        // Start path to JSON definition file
        $jsonPath = ROOT_DIR . 'themes/' . $theme . '/definitions/';

        // If this page is for a collection detail, get collection record
        if (isset($args['collection'])) {
            $page->collection = $collectionMapper->findById($args['collection']);
            $jsonPath .= 'collection/' . $page->collection->kind . '.json';
        } else {
            $jsonPath .= pathinfo($page->template, PATHINFO_FILENAME) . '.json';
        }

        if (null === $page->definition = $json->getJson($jsonPath, 'page')) {
            $this->setAlert('danger', 'Template Definition Error', $json->getErrorMessages());
        }

        // If this was for a collection, then update template file name from JSON
        if (isset($args['collection'])) {
            $page->template = $page->definition->collectionDetailTemplate;
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
        $Markdown = $this->container->markdownParser;
        $toolbox = $this->container->toolbox;

        // Create page object and populate POST data
        $page = $PageMapper->make();
        $page->id = $this->request->getParsedBodyParam('id');
        $page->collection_id = $this->request->getParsedBodyParam('collection_id');
        $page->title = $this->request->getParsedBodyParam('title');
        $page->sub_title = $this->request->getParsedBodyParam('sub_title');
        $page->slug = $toolbox->cleanUrl($this->request->getParsedBodyParam('slug'));
        $page->template = $this->request->getParsedBodyParam('template');
        $page->meta_description = $this->request->getParsedBodyParam('meta_description');

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
            $pageElement->gallery_id = $this->request->getParsedBodyParam('gallery_id')[$key];
            $pageElement->gallery_id = $this->request->getParsedBodyParam('gallery_id')[$key];
            $pageElement->image_path = $this->request->getParsedBodyParam('image_path')[$key];
            $pageElement->video_path = $this->request->getParsedBodyParam('video_path')[$key];

            // Collection ID is carrying two values: collection_id|summary_template
            if (!empty($this->request->getParsedBodyParam('element_collection_id')[$key])) {
                $collection = explode('|', $this->request->getParsedBodyParam('element_collection_id')[$key]);
                $pageElement->collection_id = $collection[0];
                $pageElement->template = $collection[1];
            } else {
                $pageElement->collection_id = null;
            }

            $pageElement = $PageElementMapper->save($pageElement);
        }

        // Redirect back to show page
        return $this->redirect('showPages');
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

        if ($this->request->getParsedBodyParam('button') === 'delete' && $this->request->getParsedBodyParam('id')) {
            // Delete page
            $page = $PageMapper->make();
            $page->id = $this->request->getParsedBodyParam('id');
            $PageMapper->delete($page);

            // Delete page elements
            $PageElementMapper->deleteElementsByPageId($this->request->getParsedBodyParam('id'));
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
     * - elementType
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
        $template .= " {{ form.elementForm(element, element.block_key, element.elementTypeOptions) }}";
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
