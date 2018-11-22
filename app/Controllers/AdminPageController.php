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
        $PageElementMapper = $mapper('PageElementMapper');

        // Fetch pages
        $pages = $PageMapper->find();

        // If we found pages, then loop through to get page elements
        if ($pages) {
            foreach ($pages as $key => $row) {
                $pages[$key]->elements = $PageElementMapper->findPageElementsByPageSectionId($row->id);
            }
        }

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
        $settings = $this->container->get('settings');

        // Fetch page, or create blank array
        if (isset($args['id'])) {
            $page = $PageMapper->findById($args['id']);
        } else {
            $page = $PageMapper->make();
        }

        // Get layout templates
        $layouts = [];
        $ignoreLayouts = ['notFound.html','_base_layout.html'];
        foreach(new \DirectoryIterator(ROOT_DIR . 'themes/' . $settings['site']['theme'] . '/templates/layouts/') as $dirObject) {
            if(
                $dirObject->isDir() ||
                $dirObject->isDot() ||
                substr($dirObject->getFilename(), 0, 1) === '.' ||
                in_array($dirObject->getFilename(), $ignoreLayouts)
            ) {
                continue;
            }

            $layouts[] = $dirObject->getFilename();
        }

        $page->themeLayouts = $layouts;

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

        // Create page
        $page = $PageMapper->make();
        $page->id = $request->getParsedBodyParam('id');
        $page->title = $request->getParsedBodyParam('title');
        $page->url = $request->getParsedBodyParam('url');
        $page->url_locked = 'N'; // TODO strtolower(trim($request->getParsedBodyParam('url_locked')));
        $page->layout = $request->getParsedBodyParam('layout');
        $page->meta_description = $request->getParsedBodyParam('meta_description');
        $page->restricted = 'N'; // TODO strtolower(trim($request->getParsedBodyParam('restricted')));

        // Prep URL
        $page->url = strtolower(trim($request->getParsedBodyParam('url')));
        $page->url = preg_replace('/[^a-z0-9\s-]/', '', $page->url);
        $page->url = preg_replace('/[\s-]+/', ' ', $page->url);
        $page->url = preg_replace('/[\s]/', '-', $page->url);

        // Save
        $page = $PageMapper->save($page);

        // Redirect back to show page
        return $response->withRedirect($this->container->router->pathFor('showPages'));
    }

    /**
     * Delete Page
     *
     * SQL Foreign Key Constraints cascade to page element records.
     * Home page is not restricted from being deleted
     */
    public function deletePage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');

        // Delete page
        $page = $PageMapper->findById($args['id']);

        // Check if page is restricted
        if ($page->restricted === 'Y') {
            return $response->withRedirect($this->container->router->pathFor('editPage', ['id' => $args['id']]));
        }

        // Delete page
        $page = $PageMapper->delete($page);

        // Redirect back to show pages
        return $response->withRedirect($this->container->router->pathFor('showPages'));
    }

    /**
     * Edit Element Content
     *
     * Edit new page element, or edit existing page element
     * Query by page_element.id, or create new content by passing in the page_id
     */
    public function editPageElement($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');
        $PageElementMapper = $mapper('PageElementMapper');

        // Fetch page element, or create blank element
        if (isset($args['id'])) {
            $pageElement = $PageElementMapper->findById($args['id']);
        } else {
            $pageElement = $PageElementMapper->make();
        }

        // Pass in page ID if missing (for new page element content)
        if (empty($pageElement->page_id)) {
            $pageElement->page_id = $request->getQueryParam('page_id');
        }

        // Get page header for display
        $page = $PageMapper->findById($pageElement->page_id);
        $pageElement->title = $page->title;

        return $this->container->view->render($response, '@admin/editPageElement.html', ['element' => $pageElement]);
    }

    /**
     * Save Element Content
     *
     * Save new page element, or update existing page element
     */
    public function savePageElement($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageElementMapper = $mapper('PageElementMapper');
        $markdown = $this->container->markdownParser;

        // Create page
        $page = $PageElementMapper->make();
        $page->id = $request->getParsedBodyParam('id');
        $page->page_id = $request->getParsedBodyParam('page_id');
        $page->name = $request->getParsedBodyParam('name');
        $page->content_raw = $request->getParsedBodyParam('content_raw');
        $page->content = $markdown->text($request->getParsedBodyParam('content_raw'));

        // Save
        $page = $PageElementMapper->save($page);

        // Redirect back to show page
        return $response->withRedirect($this->container->router->pathFor('showPages'));
    }

    /**
     * Delete Element
     */
    public function deletePageElement($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageElementMapper = $mapper('PageElementMapper');

        // Delete page element
        $pageElment = $PageElementMapper->make();
        $pageElment->id = $args['id'];
        $pageElment = $PageElementMapper->delete($pageElment);

        // Redirect back to show pages
        return $response->withRedirect($this->container->router->pathFor('showPages'));
    }
}
