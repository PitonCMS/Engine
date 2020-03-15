<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Controllers;

use Slim\Http\Response;

/**
 * Piton Navigation Controller
 *
 * Manage page navigators
 */
class AdminNavigationController extends AdminBaseController
{
    /**
     * Show Navigator
     *
     * @param  array $args Route parameter segment 'nav'
     * @return Response
     */
    public function showNavigator($args): Response
    {
        // Get dependencies and initiate $data variable
        $navMapper = ($this->container->dataMapper)('NavigationMapper');
        $definition = $this->container->jsonDefinitionHandler;
        $data = [];

        // Get navigation definitions
        if (null === $navSettings = $definition->getNavigation()) {
            $this->setAlert('danger', 'Navigation JSON Definition Error', $definition->getErrorMessages());
        }

        $data['navigators'] = $navSettings->navigators;

        // If no navigator was requested as a URL segment, load the first navigator from definitions, or falsy
        $args['nav'] = $args['nav'] ?? $data['navigators'][0] ?? [];

        // If a navigator was requested
        if (isset($args['nav'])) {
            $navigation = $navMapper->findNavHierarchy($args['nav'], null, false, false) ?? [];
            $data['navigator'] = $args['nav'];
            $data['navigation'] = $navigation;
        }

        return $this->render('navigation/navigation.html', $data);
    }

    /**
     * Save Navigation
     *
     * @param void
     * @return Resonse
     */
    public function saveNavigation(): Response
    {
        // Get dependencies
        $navigationMapper = ($this->container->dataMapper)('NavigationMapper');

        // Get POST data
        $navPost = $this->request->getParsedBodyParam('nav');
        $navigator = $this->request->getParsedBodyParam('navigator');

        // Save each nav item
        $sort = 0;
        foreach ($navPost as &$navItem) {
            // Check whether to just delete
            if (isset($navItem['delete']) && $navItem['delete'] === 'on') {
                if (is_numeric($navItem['navId'])) {
                    // This has been saved to the database, so do a physical delete
                    $navigationMapper->deleteByNavId((int) $navItem['navId']);
                }
                // Simply skip processing row if not saved to database
                continue;
            }

            $sort++;
            $nav = $navigationMapper->make();
            $nav->id = (is_numeric($navItem['navId'])) ? (int) $navItem['navId'] : null;
            $nav->navigator = $navigator;

            // Page ID 0 is for placeholder nav links, which are not joined to page table
            $nav->page_id = (is_numeric($navItem['pageId']) && $navItem['pageId'] !== '0') ? (int) $navItem['pageId'] : null;

            // Get parent nav ID if set
            // If parent ID is not numeric (new pages use a '0+x'), then get parent nav link by navPost array key, and use that nav ID
            $nav->parent_id = null;
            if (!empty($navItem['parentId']) && $navItem['parentId'] !== '0') {
                $nav->parent_id = is_numeric($navItem['parentId']) ? (int) $navItem['parentId'] : $navPost[$navItem['parentId']]['navId'];
            }

            $nav->sort = $sort;
            // $nav->title = $navItem['navTitle'];
            $nav->active = $navItem['active'] ?: 'Y';

            // Save and assign inserted nav ID for child rows
            $savedNav = $navigationMapper->save($nav);
            $navItem['navId'] = $savedNav->id;
        }

        return $this->redirect('adminNavigations', ['nav' => $navigator]);
    }
}
