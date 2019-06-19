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
        foreach ($post as $navItem) {
            $sort++;
            $nav = $navigationMapper->make();
            $nav->id = $navItem['navId'];

            // Check whether to just delete
            if ($navItem['delete'] == 'on') {
                $navigationMapper->delete($nav);
                continue;
            }

            $nav->navigator = $navigator;
            // pageId 0 is for placeholder nav links
            $nav->page_id = ($navItem['pageId'] === '0') ? null : $navItem['pageId'];
            $nav->parent_id = $navItem['parentId'];
            $nav->sort = $sort;
            $nav->title = $navItem['title'];
            $nav->active = $navItem['active'] ?: 'Y';
            $navigationMapper->save($nav);
        }

        return $this->redirect('adminNavigations', ['nav' => $navigator]);
    }
}
