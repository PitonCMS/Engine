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

        // Fetch pages
        // $page = $PageMapper->findPageByUrl($args['url']);

        // Verify we have an object, if not create one
        // if (!is_object($page)) {
        //     $page = $PageMapper->make();
        // }

        // Send 404 if not found
        // if (!isset($page->id)) {
        //     return $this->notFound($request, $response);
        // }

        // Add pagelet content
        // $page->pagelets = $this->indexPageletKeys($PageletMapper->findPageletsByPageId($page->id));

        // Make sure the .html file extension is there
        // $template = preg_replace('/\.html$/i', '', $page->template);
        // $template = $template . '.html';

        return $this->container->view->render($response, $template, ['page' => $page]);
    }
}
