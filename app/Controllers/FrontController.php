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
     * Displays page matching URL slug, or throws 404 Not Found
     * @param array $args Array of URL parameters, expecting 'slug1', 'slug2'
     */
    public function showPage($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $pageMapper = $mapper('PageMapper');
        $pageElementMapper = $mapper('PageElementMapper');
        $collectionMapper = $mapper('CollectionMapper');
        $pageSettingMapper = $mapper('PageSettingMapper');

        if (isset($args['slug2'])) {
            // This request is for a collection
            $page = $pageMapper->findPublishedCollectionPageBySlug($args['slug1'], $args['slug2']);
        } else {
            // Get page data
            $page = $pageMapper->findPublishedPageBySlug($args['slug1']);
        }

        // Send 404 if not found
        if (empty($page)) {
            return $this->notFound();
        }

        // Get elements
        $page->blocks = $this->buildElementsByBlock($pageElementMapper->findElementsByPageId($page->id));

        // Get custom fields
        $page->fields = $this->buildFields($pageSettingMapper->findPageSettings($page->id));

        return $this->render($page->template, $page);
    }
}
