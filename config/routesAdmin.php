<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
use Piton\Controllers\AdminController;
use Piton\Controllers\AdminUserController;
use Piton\Controllers\AdminPageController;
use Piton\Controllers\AdminSettingController;
use Piton\Controllers\AdminAccessController;
use Piton\Controllers\AdminCollectionController;

//
// Private secured routes
//
$app->group('/admin', function () {

    // Admin home
    $this->get('/home', function ($args) {
        return (new AdminController($this))->home();
    })->setName('adminHome');

    // User routes
    $this->group('/user', function () {
        // Show Users
        $this->get('[/]', function ($args) {
            return (new AdminUserController($this))->showUsers();
        })->setName('showUsers');

        // Save Users
        $this->post('/save', function ($args) {
            return (new AdminUserController($this))->saveUsers();
        })->add('csrfGuard')->setName('saveUsers');

        // Delete User
        $this->get('/delete/{id:[0-9]{1,}}', function ($args) {
            return (new AdminUserController($this))->deleteUser($args);
        })->setName('deleteUser');
    });
    // End user routes

    // Page route
    $this->group('/page', function () {
        // Show All Pages
        $this->get('[/]', function ($args) {
            return (new AdminPageController($this))->showPages();
        })->setName('showPages');

        // Edit or add new page. Must provide ID or page layout argument
        $this->get('/edit/{id}', function ($args) {
            return (new AdminPageController($this))->editPage($args);
        })->setName('editPage');

        // Save Page for Update, Insert, and Delete
        $this->post('/save', function ($args) {
            if ($this->request->getParsedBodyParam('button') === 'save') {
                return (new AdminPageController($this))->savePage();
            } elseif ($this->request->getParsedBodyParam('button') === 'delete') {
                return (new AdminPageController($this))->deletePage($args);
            } else {
                $notFound = $this->notFoundHanlder;
                return $notFound($this->request, $this->response);
            }
        })->add('csrfGuard')->setName('savePage');

        // Page elements
        $this->group('/element', function () {
            // Fetch element form
            $this->post('/new', function ($args) {
                return (new AdminPageController($this))->newElementForm();
            })->add('csrfGuard');

            // Delete ELement (XHR)
            $this->post('/delete', function ($args) {
                return (new AdminPageController($this))->deleteElement();
            })->add('csrfGuard');
        });
        // End page elements
    });
    // End page routes

    // Settings
    $this->group('/settings', function () {
        // Show Settings
        $this->get('[/]', function ($args) {
            return (new AdminSettingController($this))->showSettings();
        })->setName('showSettings');

        // Save Settings
        $this->post('/save', function ($args) {
            return (new AdminSettingController($this))->saveSettings();
        })->add('csrfGuard')->setName('saveSettings');

        // Theme Settings
        $this->group('/theme', function () {
            // Delete Setting
            $this->post('/delete', function ($args) {
                return (new AdminSettingController($this))->deleteThemeSetting();
            })->add('csrfGuard');
        }); // End Theme Settings
    });
    // End settings

    // Collections
    $this->group('/collection', function () {
        // Show collections
        $this->get('[/]', function ($args) {
            return (new AdminCollectionController($this))->showCollections();
        })->setName('showCollections');

        // Create or edit collection
        $this->get('/edit[/{id}]', function ($args) {
            return (new AdminCollectionController($this))->editCollection($args);
        })->setName('editCollection');

        // Save Collection, Including Deletes
        $this->post('/save', function ($args) {
            if ($this->request->getParsedBodyParam('button') === 'save') {
                return (new AdminCollectionController($this))->saveCollection();
            } elseif ($this->request->getParsedBodyParam('button') === 'delete') {
                return (new AdminCollectionController($this))->deleteCollection();
            } else {
                $notFound = $this->notFoundHanlder;
                return $notFound($this->request, $this->response);
            }
        })->add('csrfGuard')->setName('saveCollection');
    });
    // End collection
})->add(function ($request, $response, $next) {
    // Authentication
    $Security = $this->accessHandler;

    if (!$Security->isAuthenticated()) {
        // Failed authentication, redirect to login
        return $response->withRedirect($this->router->pathFor('showLoginForm'));
    }

    // Next call
    return $next($request, $response);
})->add(function ($request, $response, $next) {
    // Add http header to prevent back button access to admin
    $newResponse = $response->withAddedHeader("Cache-Control", "private, no-cache, no-store, must-revalidate");

    // Next call
    return $next($request, $newResponse);
});

//
// Public unsecured routes
//

// Login page with form to submit email
$app->get('/letmein', function ($args) {
    return (new AdminAccessController($this))->showLoginForm();
})->setName('showLoginForm');

// Accept and validate email, and send login token
$app->post('/requestlogintoken/', function ($args) {
    return (new AdminAccessController($this))->requestLoginToken();
})->add('csrfGuard')->setName('requestLoginToken');

// Accept and validate login token and set session
$app->get('/processlogintoken/{token:[a-zA-Z0-9]{64}}', function ($args) {
    return (new AdminAccessController($this))->processLoginToken($args);
})->setName('processLoginToken');

// Logout
$app->get('/logout', function ($args) {
    return (new AdminAccessController($this))->logout();
})->setName('logout');
