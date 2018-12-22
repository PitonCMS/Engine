<?php
/**
 * Piton Admin Page Controller
 */
namespace Piton\Controllers;

class AdminPageController extends AdminBaseController
{
    /**
     * Show Pages
     *
     * Show pages with child elements
     */
    public function showPages()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');

        // Fetch pages
        $pages = $PageMapper->find();

        return $this->render('showPages.html', $pages);
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
        $PageJson = $this->container->pageLayoutJson;

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
        if (null === $page->definition = $PageJson->getPageLayoutDefinition($page->layout)) {
            $this->setAlert('danger', 'Layout Definition Error', $PageJson->getErrorMessages());
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
        $markdown = $this->container->markdownParser;

        // Create page object and populate POST data
        $page = $PageMapper->make();
        $page->id = $this->request->getParsedBodyParam('id');
        $page->title = $this->request->getParsedBodyParam('title');
        $page->url_locked = 'N'; // TODO strtolower(trim($this->request->getParsedBodyParam('url_locked')));
        $page->layout = $this->request->getParsedBodyParam('layout');
        $page->meta_description = $this->request->getParsedBodyParam('meta_description');

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
            $pageElement->content = $markdown->text($this->request->getParsedBodyParam('content_raw')[$key]);
            $pageElement->excerpt = null; // Get excerpt substr (60) from ->content
            $pageElement->collection_id = $this->request->getParsedBodyParam('collection_id')[$key];
            $pageElement->media_id = $this->request->getParsedBodyParam('media_id')[$key];
            $pageElement->media_path = $this->request->getParsedBodyParam('media_path')[$key];
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
    public function deletePage($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');
        $PageElementMapper = $mapper('PageElementMapper');

        // Verify we have a page ID to act on
        if (!is_numeric($args['id'])) {
            throw new \Exception("Page to delete not found", 1);
        }

        // Delete page
        $page = $PageMapper->make();
        $page->id = $args['id'];
        $page = $PageMapper->delete($page);

        // Delete page elements
        $PageElementMapper->deletePageElementsByPageId($page->id);

        // Redirect back to show pages
        return $this->redirect('showPages');
    }

    /**
     * Fetch New Element Form
     *
     * Renders new element form with initial values, and returns via Ajax to browser.
     * At a minimum, the element form is expecting these values:
     * - sectionCodeName
     * - elementType
     * - elementSort
     * - elementTypeOptions | optional, comma separated list of approved element types
     */
    public function fetchElementForm()
    {
        $parsedBody = $this->request->getParsedBody();

        $form['section_name'] = $parsedBody['sectionCodeName'];
        $form['element_type'] = $parsedBody['elementType'];
        $form['element_sort'] = 1;

        // Only include element type options if the string is not empty
        if (!empty($parsedBody['elementTypeOptions'])) {
            $form['elementTypeOptions'] = explode(',', $parsedBody['elementTypeOptions']);
        }

        $elementFormHtml = $this->container->view->fetch('@admin/editElementFormLoad.html', ['element' => $form]);

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
