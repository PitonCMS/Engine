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
     */
    public function showNavigator($args)
    {
        // Get dependencies and initiate $data variable
        $navMapper = ($this->container->dataMapper)('NavigationMapper');
        $json = $this->container->json;
        $data = [];

        // Get navigation definitions
        $jsonPath = ROOT_DIR . 'structure/definitions/navigation.json';
        if (null === $navSettings = $json->getJson($jsonPath)) {
            $this->setAlert('danger', 'Navigation JSON Definition Error', $json->getErrorMessages());
        }

        $data['navigators'] = $navSettings->navigators;

        // If a navigator was requested
        if (isset($args['nav'])) {
            $navigation = $navMapper->findNavHierarchy($args['nav'], null, false, false);
            $data['navigator'] = $args['nav'];
            $data['navigation'] = $navigation;
        }

        return $this->render('navigation/navigation.html', $data);
    }

    /**
     * Save Navigation
     */
    public function saveNavigation()
    {
        // Get dependencies
        $navigationMapper = ($this->container->dataMapper)('NavigationMapper');

        // Get POST data
        $post = $this->request->getParsedBodyParam('navSet');
        $navigator = $this->request->getParsedBodyParam('navigator');

        // Save each nav item
        $sort = 0;
        foreach ($post as $postKey => &$navItem) {
            // Check whether to just delete
            if ($navItem['delete'] == 'on') {
                if (is_numeric($navItem['navId'])) {
                    $navigationMapper->deleteByNavId($navItem['navId']);
                }
                continue;
            }

            $sort++;
            $nav = $navigationMapper->make();
            $nav->id = $navItem['navId'];
            $nav->navigator = $navigator;

            // pageId 0 is for placeholder nav links, which are not joined to page table
            $nav->page_id = ($navItem['pageId'] === '0') ? null : $navItem['pageId'];

            // Get parent ID if set
            // If parent ID is not numeric, then get parent nav link by post array key, and use that nav ID
            $nav->parent_id = null;
            if (!empty($navItem['parentId'])) {
                $nav->parent_id = is_numeric($navItem['parentId']) ? (int) $navItem['parentId'] : $post[$navItem['parentId']]['navId'];
            }

            $nav->sort = $sort;
            $nav->title = $navItem['title'];
            $nav->active = $navItem['active'] ?: 'Y';
            $savedNav = $navigationMapper->save($nav);
            $navItem['navId'] = $savedNav->id;
        }

        return $this->redirect('adminNavigations', ['nav' => $navigator]);
    }
}
