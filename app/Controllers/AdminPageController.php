<?php
/**
 * Admin Page Controller
 */
namespace Piton\Controllers;

class AdminPageController extends BaseController
{
    /**
     * Show Pages
     *
     * Show pages with child elements
     */
    public function showPages($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');

        // Fetch pages
        $pages = $PageMapper->find();

        return $this->container->view->render($response, '@admin/showPages.html', ['pages' => $pages]);
    }

    /**
     * Edit Page
     *
     * Create new page, or edit existing page
     */
    public function editPage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');
        $PageSectionElementMapper = $mapper('PageSectionElementMapper');
        $settings = $this->container->get('settings');

        // Fetch page, or create new page
        if (is_numeric($args['id'])) {
            $page = $PageMapper->findById($args['id']);
            $page->elements = $PageSectionElementMapper->getSectionElementsByPageId($args['id']);
        } elseif (is_string($args['id'])) {
            // New page
            $page = $PageMapper->make();
            $page->layout = $args['id'] . '.html';
        }

        // Get page definition
        // TODO Handle error if no layout json file found
        $layout = pathinfo($page->layout, PATHINFO_FILENAME);
        $layoutDefintion = ROOT_DIR . 'themes/' . $settings['site']['theme'] . '/templates/layouts/' . $layout . '.json';
        $pageDefinition = file_get_contents($layoutDefintion);
        $page->definition = json_decode($pageDefinition, true);

        return $this->container->view->render($response, '@admin/editPage.html', ['page' => $page]);
    }

    /**
     * Save Page
     *
     * Create new page, or update existing page
     */
    public function savePage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');
        $PageElementMapper = $mapper('PageElementMapper');
        $PageSectionElementMapper = $mapper('PageSectionElementMapper');
        $markdown = $this->container->markdownParser;

        // Create page object and populate POST data
        $page = $PageMapper->make();
        $page->id = $request->getParsedBodyParam('id');
        $page->title = $request->getParsedBodyParam('title');
        $page->url_locked = 'N'; // TODO strtolower(trim($request->getParsedBodyParam('url_locked')));
        $page->layout = $request->getParsedBodyParam('layout');
        $page->meta_description = $request->getParsedBodyParam('meta_description');

        // Prep URL
        $page->url = strtolower(trim($request->getParsedBodyParam('url')));
        $page->url = preg_replace('/[^a-z0-9\s-]/', '', $page->url);
        $page->url = preg_replace('/[\s-]+/', ' ', $page->url);
        $page->url = preg_replace('/[\s]/', '-', $page->url);

        // Save Page and get ID
        $page = $PageMapper->save($page);

        // Save page section elements
        foreach ($request->getParsedBodyParam('section_name') as $key => $value) {
            // Save element
            $pageElement = $PageElementMapper->make();
            $pageElement->id = $request->getParsedBodyParam('element_id')[$key];
            $pageElement->element_type = $request->getParsedBodyParam('element_type')[$key];
            $pageElement->title = $request->getParsedBodyParam('element_title')[$key];
            $pageElement->content_raw = $request->getParsedBodyParam('content_raw')[$key];
            $pageElement->content = $markdown->text($request->getParsedBodyParam('content_raw')[$key]);
            $pageElement->collection_id = $request->getParsedBodyParam('collection_id')[$key];
            $pageElement->media_id = $request->getParsedBodyParam('media_id')[$key];
            $pageElement->media_path = $request->getParsedBodyParam('media_path')[$key];
            $pageElement = $PageElementMapper->save($pageElement);

            // Save section element map
            $pageSectionElementMap = $PageSectionElementMapper->make();
            $pageSectionElementMap->id = $request->getParsedBodyParam('section_element_id')[$key];
            $pageSectionElementMap->page_id = $page->id;
            $pageSectionElementMap->section_name = $request->getParsedBodyParam('section_name')[$key];
            $pageSectionElementMap->element_id = $pageElement->id;
            $pageSectionElementMap->element_sort = $request->getParsedBodyParam('element_sort')[$key];
            $PageSectionElementMapper->save($pageSectionElementMap);
        }

        // Redirect back to show page
        return $response->withRedirect($this->container->router->pathFor('showPages'));
    }

    /**
     * Delete Page
     *
     * Home page is not restricted from being deleted
     */
    public function deletePage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');
        $PageSectionElementMapper = $mapper('PageSectionElementMapper');

        // Verify we have a page ID to act on
        if (!is_numeric($args['id'])) {
            $this->notFound($request, $response);
        }

        // Delete page
        $page = $PageMapper->make();
        $page->id = $args['id'];
        $page = $PageMapper->delete($page);

        // Delete page section element map
        $PageSectionElementMapper->deleteSectionElementsByPageId($page->id);

        // Redirect back to show pages
        return $response->withRedirect($this->container->router->pathFor('showPages'));
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
    public function fetchElementForm($request, $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        $form['sectionCodeName'] = $parsedBody['sectionCodeName'];
        $form['elementType'] = $parsedBody['elementType'];
        $form['elementSort'] = 1;

        // Only include element type options if the string is not empty
        if (!empty($parsedBody['elementTypeOptions'])) {
            $form['elementTypeOptions'] = explode(',', $parsedBody['elementTypeOptions']);
        }

        $elementFormHtml = $this->container->view->fetch('@admin/editElementFormLoad.html', ['data' => $form]);

        // Set the response type
        $r = $response->withHeader('Content-Type', 'application/json');

        return $r->write(json_encode(["html" => $elementFormHtml]));
    }

    /**
     * Delete Section Element
     *
     * Ajax request
     */
    public function deletePageSectionElement($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageSectionElementMapper = $mapper('PageSectionElementMapper');

        // Delete section element
        $sectionElement = $PageSectionElementMapper->make();
        $sectionElement->id = $args['id'];
        $PageSectionElementMapper->delete($sectionElement);

        // Set the response type
        $r = $response->withHeader('Content-Type', 'application/json');

        return $r->write(json_encode(["status" => "success"]));
    }
}
