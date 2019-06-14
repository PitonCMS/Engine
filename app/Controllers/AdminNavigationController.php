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
 * Manages page navigators
 */
class AdminNavigationController extends AdminBaseController
{
    /**
     * Show All Media
     */
    public function showNavigator($args)
    {
        $navMapper = ($this->container->dataMapper)('NavigationMapper');
        $json = $this->container->json;
        $data = [];

        // Get navigation definitions
        $jsonPath = ROOT_DIR . 'structure/definitions/navigation.json';
        if (null === $navSettings = $json->getJson($jsonPath)) {
            $this->setAlert('danger', 'Navigation JSON Definition Error', $json->getErrorMessages());
        }

        $data['navigators'] = $navSettings->navigators;

        // Verify a navigator was provided
        if (isset($args['nav'])) {
            $navigation = $navMapper->findNavHierarchy($args['nav']);
            $data['navigator'] = $args['nav'];
            $data['navigation'] = $navigation;
        }

        return $this->render('navigation/navigation.html', $data);
    }

    /**
     * Save Navigation
     *
     * Save navigation
     */
    public function saveNavigation()
    {
        $navigationMapper = ($this->container->dataMapper)('NavigationMapper');

        $post = $this->request->getParsedBodyParam('navSet');
        $navigator = $this->request->getParsedBodyParam('navigator');

        // Save each nav item
        $sort = 1;
        foreach ($post as $navItem) {
            // echo "saving...";
            $sort++;
            $nav = $navigationMapper->make();
            $nav->id = $navItem['navId'];

            // Check whether to just delete
            if ($navItem['delete'] == 'on') {
                $navigationMapper->delete($nav);
                continue;
            }

            $nav->navigator = $navigator;
            $nav->page_id = $navItem['pageId'];
            $nav->parent_id = $navItem['parentId'];
            $nav->sort = $sort;
            $nav->title = $navItem['title'];
            $nav->active = $navItem['active'] ?: 'Y';
            $navigationMapper->save($nav);
        }

        return $this->redirect('adminNavigations', ['nav' => $navigator]);
    }
}
