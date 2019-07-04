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
        $definition = $this->container->definition;
        $data = [];

        // Get navigation definitions
        if (null === $navSettings = $definition->getNavigation()) {
            $this->setAlert('danger', 'Navigation JSON Definition Error', $definition->getErrorMessages());
        }

        $data['navigators'] = $navSettings->navigators;

        // If no navigator was requested as a URL segment, load the first navigator from definitions, or falsy
        $args['nav'] = $args['nav'] ?? $data['navigators'][0] ?? 0;


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
        $post = $this->request->getParsedBodyParam('nav');
        $navigator = $this->request->getParsedBodyParam('navigator');

        // Save each nav item
        $sort = 0;
        foreach ($post as &$navItem) {
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
            $nav->title = $navItem['navTitle'];
            $nav->active = $navItem['active'] ?: 'Y';
            $savedNav = $navigationMapper->save($nav);
            $navItem['navId'] = $savedNav->id;
        }

        return $this->redirect('adminNavigations', ['nav' => $navigator]);
    }
}
