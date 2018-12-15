<?php
/**
 * Front End Controller
 *
 * Primary public facing controller
 */
namespace Piton\Controllers;

class FrontController extends BaseController
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
        $page = $PageMapper->findPageData($args['url']);

        // Send 404 if not found
        if (!isset($page)) {
            return $this->notFound($request, $response);
        }

        return $this->container->view->render($response, 'layouts/' . $page['layout'], ['page' => $page]);
    }
}
