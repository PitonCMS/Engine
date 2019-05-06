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
use Piton\Controllers\AdminMediaController;
use Piton\Controllers\AdminMessageController;

//
// Private secured routes
//
$app->group('/admin', function () {

    // Admin home
    $this->get('/home', function ($args) {
        return (new AdminController($this))->home();
    })->setName('adminHome');

    $this->group('/help', function () {
        $this->get('/release/{release:\d+\.\d+\.\d+}', function ($args) {
            return (new AdminController($this))->release($args);
        })->setName('adminEngineRelease');
    });
    // End Help

    // User routes
    $this->group('/user', function () {
        // Show Users
        $this->get('[/]', function ($args) {
            return (new AdminUserController($this))->showUsers();
        })->setName('adminUsers');

        // Save Users
        $this->post('/save', function ($args) {
            return (new AdminUserController($this))->saveUsers();
        })->add('csrfGuard')->setName('adminSaveUsers');

        // Delete User
        $this->get('/delete/{id:[0-9]{1,}}', function ($args) {
            return (new AdminUserController($this))->deleteUser($args);
        })->setName('adminDeleteUser');
    });
    // End user routes

    // Page route
    $this->group('/page', function () {
        // Show All Pages
        $this->get('[/]', function ($args) {
            return (new AdminPageController($this))->showPages();
        })->setName('adminPages');

        // Edit or add new page. Must provide ID or page layout argument
        $this->get('/edit[/{id:[0-9]+}]', function ($args) {
            return (new AdminPageController($this))->editPage($args);
        })->setName('adminEditPage');

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
        })->add('csrfGuard')->setName('adminSavePage');

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
        })->setName('adminSettings');

        // Save Settings
        $this->post('/save', function ($args) {
            return (new AdminSettingController($this))->saveSettings();
        })->add('csrfGuard')->setName('adminSaveSettings');
    });
    // End settings

    // Collections
    $this->group('/collection', function () {
        // Show collections
        $this->get('[/]', function ($args) {
            return (new AdminCollectionController($this))->showCollections();
        })->setName('adminCollections');

        // Create or edit collection
        $this->get('/edit[/{id:[0-9]+}]', function ($args) {
            return (new AdminCollectionController($this))->editCollection($args);
        })->setName('adminEditCollection');

        // Confirm delete of collection and pages
        $this->get('/delete/{id:[0-9]+}', function ($args) {
            return (new AdminCollectionController($this))->confirmDeleteCollection($args);
        })->setName('adminConfirmDeleteCollection');

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
        })->add('csrfGuard')->setName('adminSaveCollection');
    });
    // End collection

    // Media
    $this->group('/media', function () {
        // Show all media
        $this->get('[/]', function ($args) {
            return (new AdminMediaController($this))->showMedia();
        })->setName('adminShowMedia');

        // Show all media in modal asynchronously
        $this->get('/get', function ($args) {
            return (new AdminMediaController($this))->getMedia();
        })->setName('adminGetMedia');

        // Upload Form
        $this->get('/form', function ($args) {
            return (new AdminMediaController($this))->uploadMediaForm();
        })->setName('adminFileUploadForm');

        // File upload
        $this->post('/upload', function ($args) {
            return (new AdminMediaController($this))->uploadMedia();
        })->add('csrfGuard')->setName('adminFileUpload');

        // Media save
        $this->post('/save', function ($args) {
            if ($this->request->getParsedBodyParam('button') === 'save') {
                return (new AdminMediaController($this))->saveMedia();
            } elseif ($this->request->getParsedBodyParam('button') === 'delete') {
                return (new AdminMediaController($this))->deleteMedia();
            } else {
                $notFound = $this->notFoundHandler;
                return $notFound($this->request, $this->response);
            }
        })->add('csrfGuard')->setName('adminMediaSave');

        // Media categories
        $this->group('/category', function () {
            $this->get('/edit', function ($args) {
                return (new AdminMediaController($this))->editMediaCategories();
            })->setName('adminEditMediaCategories');

            // Save media category
            $this->post('/save', function ($args) {
                return (new AdminMediaController($this))->saveMediaCategories();
            })->add('csrfGuard')->setName('adminSaveMediaCategories');

            // Delete media category
            $this->post('/delete', function ($args) {
                return (new AdminMediaController($this))->deleteMediaCategory();
            })->add('csrfGuard')->setName('adminDeleteMediaCategory');
        });
    });
    // End media

    // Messages
    $this->group('/message', function () {
        // Show all messages
        $this->get('[/]', function ($args) {
            return (new AdminMessageController($this))->showMessages();
        })->setName('adminMessages');

        // Save message changes
        $this->post('/save', function ($args) {
            if ($this->request->getParsedBodyParam('button') === 'toggle') {
                return (new AdminMessageController($this))->toggleStatus();
            } elseif ($this->request->getParsedBodyParam('button') === 'delete') {
                return (new AdminMessageController($this))->delete();
            } else {
                $notFound = $this->notFoundHandler;
                return $notFound($this->request, $this->response);
            }
        })->add('csrfGuard')->setName('adminMessageSave');
    });
    // End messages
})->add(function ($request, $response, $next) {
    // Authentication
    $Security = $this->accessHandler;

    if (!$Security->isAuthenticated()) {
        // Failed authentication, redirect to login
        return $response->withRedirect($this->router->pathFor('adminLoginForm'));
    }

    // Next call
    return $next($request, $response);
})->add(function ($request, $response, $next) {
    // Add http header to prevent back button access to admin
    $response = $next($request, $response);
    return $response->withAddedHeader("Cache-Control", "private, no-cache, no-store, must-revalidate");
});

//
// Public unsecured routes
//

// Login page with form to submit email
$app->get('/letmein', function ($args) {
    return (new AdminAccessController($this))->showLoginForm();
})->setName('adminLoginForm');

// Accept and validate email, and send login token
$app->post('/requestlogintoken/', function ($args) {
    return (new AdminAccessController($this))->requestLoginToken();
})->add('csrfGuard')->setName('adminRequestLoginToken');

// Accept and validate login token and set session
$app->get('/processlogintoken/{token:[a-zA-Z0-9]{64}}', function ($args) {
    return (new AdminAccessController($this))->processLoginToken($args);
})->setName('adminProcessLoginToken');

// Logout
$app->get('/logout', function ($args) {
    return (new AdminAccessController($this))->logout();
})->setName('adminLogout');
