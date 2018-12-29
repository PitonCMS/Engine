<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Controllers;

/**
 * Piton Front End Controller
 *
 */
class FrontController extends FrontBaseController
{
    /**
     * Show Page
     *
     * Displays page matching URL, or throws 404 Not Found
     * @param array $args Array of URL parameters, expecting 'url'
     */
    public function showPage($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $Page = $mapper('PageMapper');
        $PageElement = $mapper('PageElementMapper');

        // Get page data
        $page = $Page->findPublishedPageByUrl($args['url']);

        // Send 404 if not found
        if (empty($page)) {
            return $this->notFound();
        }

        // Get elements
        $page->sections = $this->buildElementsBySection($PageElement->findElementsByPageId($page->id));

        return $this->render($page->layout, $page);
    }
}
