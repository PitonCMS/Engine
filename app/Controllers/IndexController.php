<?php
/**
 * Index Controller
 *
 * Primary public facing controller
 */
namespace Piton\Controllers;

class IndexController extends BaseController
{
    /**
     * Show Dynamic Page
     */
    public function showPage($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');
        $PageElementMapper = $mapper('PageElementMapper');

        // Fetch pages
        $page = $PageMapper->findPageByUrl($args['url']);

        // Send 404 if not found
        if (!$page) {
            return $this->notFound($request, $response);
        }

        // Load page elements
        $page->elements = $this->indexPageletKeys($PageElementMapper->findPageElementsByPageId($page->id));

        // Make sure the .html file extension is appended to the template name
        $template = preg_replace('/\.html$/i', '', $page->template);
        $template = $template . '.html';

        return $this->container->view->render($response, $template, ['page' => $page]);
    }
}
