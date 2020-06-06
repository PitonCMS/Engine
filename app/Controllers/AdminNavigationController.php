<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2020 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Controllers;

use Slim\Http\Response;
use Exception;

/**
 * Piton Navigation Controller
 *
 * Manage page navigators
 */
class AdminNavigationController extends AdminBaseController
{
    /**
     * Show Navigators
     *
     * @param  void
     * @return Response
     */
    public function showNavigators(): Response
    {
        // Get dependencies
        $navigators = ($this->container->jsonDefinitionHandler)->getNavigation();
        $navigators = $navigators->navigators ?? null;

        return $this->render('navigation/navigation.html', ['navigators' => $navigators]);
    }

    /**
     * Edit Navigator
     *
     * @param  array $args
     * @return Response
     */
    public function editNavigator($args): Response
    {
        // Get dependencies
        $navMapper = ($this->container->dataMapper)('NavigationMapper');
        $pageMapper = ($this->container->dataMapper)('PageMapper');
        $collectioMapper = ($this->container->dataMapper)('CollectionMapper');

        if (null === $navivation = ($this->container->jsonDefinitionHandler)->getNavigation()) {
            throw new Exception("Invalid navigator definition");
        }

        $navs = $navivation->navigators;
        $navs = array_combine(array_column($navs, 'key'), $navs);

        $data['pages'] = $pageMapper->findPages();
        $data['collections'] = $collectioMapper->find();
        $data['navigation'] = $navMapper->findNavHierarchy($args['navigator'], null, false) ?? [];
        $data['navDefinition'] = $navs[$args['navigator']];

        return $this->render('navigation/navigationEdit.html', $data);
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
        $navigation = $this->request->getParsedBodyParam('nav');
        $navigator = $this->request->getParsedBodyParam('navigator');

        // Save each nav item
        $index = 0;
        foreach ($navigation as &$navItem) {
            $index++;
            $nav = $navigationMapper->make();
            $nav->id = (is_numeric($navItem['navId'])) ? (int) $navItem['navId'] : null;
            $nav->navigator = $navigator;

            // Page ID 0 is for placeholder nav links, which are not joined to page table
            $nav->page_id = (is_numeric($navItem['pageId'])) ? (int) $navItem['pageId'] : null;
            $nav->parent_id = (is_numeric($navItem['parentId'])) ? (int) $navItem['parentId'] : null;
            $nav->sort = $index;
            $nav->title = trim($navItem['navTitle']) ?? null;
            $nav->url = $navItem['url'] ?? null;
            $nav->collection_id = $navItem['collectionId'] ?? null;

            // Save and assign inserted nav ID for child rows
            $savedNav = $navigationMapper->save($nav);
            $navItem['navId'] = $savedNav->id;
        }

        return $this->redirect('adminNavigation', ['navigator' => $navigator]);
    }
}
