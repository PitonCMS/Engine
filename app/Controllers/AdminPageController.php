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
     * Show pages with sections and elements
     */
    public function showPages()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $Page = $mapper('PageMapper');
        $PageElement = $mapper('PageElementMapper');

        // Fetch pages
        $pages = $Page->find();

        // Fetch all section elements for each page
        if ($pages) {
            foreach ($pages as $key => $row) {
                $pages[$key]->sections = $this->buildElementsBySection($PageElement->findElementsByPageId($row->id));
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
        $PageMapper = $mapper('PageMapper');
        $PageElementMapper = $mapper('PageElementMapper');
        $Json = $this->container->json;

        // Fetch page, or create new page
        if (is_numeric($args['id'])) {
            $page = $PageMapper->findById($args['id']);
            $page->elements = $PageElementMapper->findElementsByPageId($args['id']);
        } elseif (is_string($args['id'])) {
            // New page
            $page = $PageMapper->make();
            $page->layout = $args['id'] . '.html';
        }

        // Get page layout definition
        if (null === $page->definition = $Json->getPageLayoutDefinition($page->layout)) {
            $this->setAlert('danger', 'Layout Definition Error', $Json->getErrorMessages());
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
        $Toolbox = $this->container->toolbox;

        // Create page object and populate POST data
        $page = $PageMapper->make();
        $page->id = $this->request->getParsedBodyParam('id');
        $page->title = $this->request->getParsedBodyParam('title');
        $page->url_locked = 'N'; // TODO strtolower(trim($this->request->getParsedBodyParam('url_locked')));
        $page->layout = $this->request->getParsedBodyParam('layout');
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

        // Prep URL
        $page->url = strtolower(trim($this->request->getParsedBodyParam('url')));
        $page->url = preg_replace('/[^a-z0-9\s-]/', '', $page->url);
        $page->url = preg_replace('/[\s-]+/', ' ', $page->url);
        $page->url = preg_replace('/[\s]/', '-', $page->url);

        // Save Page and get ID
        $page = $PageMapper->save($page);

        // Save page section elements
        foreach ($this->request->getParsedBodyParam('section_name') as $key => $value) {
            // Save element
            $pageElement = $PageElementMapper->make();
            $pageElement->id = $this->request->getParsedBodyParam('element_id')[$key];
            $pageElement->page_id = $page->id;
            $pageElement->section_name = $this->request->getParsedBodyParam('section_name')[$key];
            $pageElement->element_type = $this->request->getParsedBodyParam('element_type')[$key];
            $pageElement->element_sort = $this->request->getParsedBodyParam('element_sort')[$key];
            $pageElement->title = $this->request->getParsedBodyParam('element_title')[$key];
            $pageElement->content_raw = $this->request->getParsedBodyParam('content_raw')[$key];
            $pageElement->content = $Markdown->text($this->request->getParsedBodyParam('content_raw')[$key]);
            $pageElement->excerpt = $Toolbox->truncateHtmlText($pageElement->content, 60);
            $pageElement->collection_id = $this->request->getParsedBodyParam('collection_id')[$key];
            $pageElement->gallery_id = $this->request->getParsedBodyParam('gallery_id')[$key];
            $pageElement->image_path = $this->request->getParsedBodyParam('image_path')[$key];
            $pageElement->video_path = $this->request->getParsedBodyParam('video_path')[$key];
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
            $page = $PageMapper->delete($page);

            // Delete page elements
            $PageElementMapper->deleteElementsByPageId($page->id);
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
     * - sectionCodeName
     * - elementType
     * - elementSort
     * - elementTypeOptions | optional, comma separated list of approved element types
     */
    public function newElementForm()
    {
        $parsedBody = $this->request->getParsedBody();

        $form['section_name'] = $parsedBody['sectionCodeName'];
        $form['element_type'] = $parsedBody['elementType'];
        $form['element_sort'] = 1;

        // Only include element type options if the string is not empty
        if (!empty($parsedBody['elementTypeOptions'])) {
            $form['elementTypeOptions'] = explode(',', $parsedBody['elementTypeOptions']);
        }

        $template = '{% import "@admin/editElementMacro.html" as form %}';
        $template .= " {{ form.elementForm(element, element.section_name, elementTypeOptions) }}";
        $elementFormHtml = $this->container->view->fetchFromString($template, ['element' => $form]);

        // Set the response type
        $r = $this->response->withHeader('Content-Type', 'application/json');

        return $r->write(json_encode(["html" => $elementFormHtml]));
    }

    /**
     * Delete Element
     *
     * Ajax request
     */
    public function deleteElement($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageElement = $mapper('PageElementMapper');

        // Delete section element
        $sectionElement = $PageElement->make();
        $sectionElement->id = $args['id'];
        $PageElement->delete($sectionElement);

        // Set the response type
        $r = $this->response->withHeader('Content-Type', 'application/json');

        return $r->write(json_encode(["status" => "success"]));
    }
}
