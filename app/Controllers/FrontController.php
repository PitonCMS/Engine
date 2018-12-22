<?php
/**
 * Piton Front End Controller
 *
 * Primary public facing controller
 */
namespace Piton\Controllers;

class FrontController extends FrontBaseController
{
    /**
     * Show Dynamic Page
     */
    public function showPage($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $PageMapper = $mapper('PageMapper');

        // Get page data
        $page = $PageMapper->findPageData($args['url']);

        // Send 404 if not found
        if (!isset($page)) {
            return $this->notFound();
        }

        return $this->render($page['layout'], $page);
    }
}
