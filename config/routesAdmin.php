<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

use Piton\Controllers\AdminController;
use Piton\Controllers\AdminUserController;
use Piton\Controllers\AdminPageController;
use Piton\Controllers\AdminNavigationController;
use Piton\Controllers\AdminSettingController;
use Piton\Controllers\AdminAccessController;
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

    // Page route
    $this->group('/page', function () {
        // Show All Pages
        $this->get('[/]', function ($args) {
            return (new AdminPageController($this))->showPages($args);
        })->setName('adminPage');

        // Edit or add new page. Must provide ID or page layout argument
        $this->get('/edit[/{id:[0-9]+}]', function ($args) {
            $args['type'] = 'page';
            return (new AdminPageController($this))->editPage($args);
        })->setName('adminPageEdit');

        // Save Page for Update or Insert
        $this->post('/save', function ($args) {
            return (new AdminPageController($this))->savePage();
        })->add('csrfGuardHandler')->setName('adminPageSave');

        // Delete page
        $this->post('/delete', function ($args) {
            return (new AdminPageController($this))->deletePage($args);
        })->add('csrfGuardHandler')->setName('adminPageDelete');

        // Page elements
        $this->group('/element', function () {
            // Fetch element form
            $this->post('/new', function ($args) {
                return (new AdminPageController($this))->newElementForm();
            })->add('csrfGuardHandler')->setName('adminPageElementNew');

            // Delete ELement (XHR)
            $this->post('/delete', function ($args) {
                return (new AdminPageController($this))->deleteElement();
            })->add('csrfGuardHandler')->setName('adminPageElementDelete');
        });
        // End page elements
    });
    // End page routes

    // Collection routes
    $this->group('/collection', function () {
        // CRUD collection
        $this->get('/edit[/{id:[0-9]+}]', function ($args) {
            return (new AdminPageController($this))->editCollection($args);
        })->setName('adminCollectionEdit');

        // Save collection
        $this->post('/save', function ($args) {
            return (new AdminPageController($this))->saveCollection();
        })->add('csrfGuardHandler')->setName('adminCollectionSave');

        // Delete collection
        $this->post('/delete', function ($args) {
            return (new AdminPageController($this))->deleteCollection();
        })->add('csrfGuardHandler')->setName('adminCollectionDelete');

        // Collection Pages
        $this->group('/page', function () {
            // Edit or add new collection page. Must provide ID or page layout argument
            $this->get('/edit[/{id:[0-9]+}]', function ($args) {
                $args['type'] = 'collection';
                return (new AdminPageController($this))->editPage($args);
            })->setName('adminCollectionPageEdit');

            // Save collection page for Update or Insert
            $this->post('/save', function ($args) {
                return (new AdminPageController($this))->savePage();
            })->add('csrfGuardHandler')->setName('adminCollectionPageSave');

            // Delete collection page
            $this->post('/delete', function ($args) {
                return (new AdminPageController($this))->deletePage($args);
            })->add('csrfGuardHandler')->setName('adminCollectionPageDelete');
        });

        // Show all collection pages, optionally by category
        $this->get('[/{collectionSlug}]', function ($args) {
            return (new AdminPageController($this))->showCollectionPages($args);
        })->setName('adminCollection');
    });
    // End collection

    // Navigation route
    $this->group('/navigation', function () {
        // Show Navigations
        $this->get('[/{nav:[a-zA-Z]+}]', function ($args) {
            return (new AdminNavigationController($this))->showNavigator($args);
        })->setName('adminNavigation');

        // Save Navigation
        $this->post('/save', function ($args) {
            return (new AdminNavigationController($this))->saveNavigation();
        })->add('csrfGuardHandler')->setName('adminNavigationSave');
    });
    // End Navigation

    // Media
    $this->group('/media', function () {
        // Show all media
        $this->get('[/]', function ($args) {
            return (new AdminMediaController($this))->showMedia();
        })->setName('adminMedia');

        // Show all media in modal asynchronously
        $this->get('/get', function ($args) {
            return (new AdminMediaController($this))->getMedia($args);
        })->setName('adminMediaGet');

        // File upload
        $this->post('/upload', function ($args) {
            return (new AdminMediaController($this))->uploadMedia();
        })->add('csrfGuardHandler')->setName('adminMediaUpload');

        // Media save
        $this->post('/save', function ($args) {
            return (new AdminMediaController($this))->saveMedia();
        })->add('csrfGuardHandler')->setName('adminMediaSave');

        // Media delete
        $this->post('/delete', function ($args) {
            return (new AdminMediaController($this))->deleteMedia();
        })->add('csrfGuardHandler')->setName('adminMediaDelete');

        // Media categories
        $this->group('/category', function () {
            $this->get('/edit', function ($args) {
                return (new AdminMediaController($this))->editMediaCategories();
            })->setName('adminMediaCategoryEdit');

            // Save media category
            $this->post('/save', function ($args) {
                return (new AdminMediaController($this))->saveMediaCategories();
            })->add('csrfGuardHandler')->setName('adminMediaCategorySave');

            // Delete media category
            $this->post('/delete', function ($args) {
                return (new AdminMediaController($this))->deleteMediaCategory();
            })->add('csrfGuardHandler')->setName('adminDeleteMediaCategory');
        });
    });
    // End media

    // Messages
    $this->group('/message', function () {
        // Show messages
        $this->get('[/[{status:new|read}]]', function ($args) {
            $args['status'] = $args['status'] ?? 'new';
            return (new AdminMessageController($this))->showMessages($args);
        })->setName('adminMessages');

        // Save message status changes
        $this->post('/save', function ($args) {
            return (new AdminMessageController($this))->toggleStatus();
        })->add('csrfGuardHandler')->setName('adminSaveMessage');

        // Delete message
        $this->post('/delete', function ($args) {
            return (new AdminMessageController($this))->delete();
        })->add('csrfGuardHandler')->setName('adminDeleteMessage');
    });
    // End messages

    // Tools routes
    $this->group('/tools', function () {
        // Settings
        $this->group('/settings', function () {
            // Show Settings
            $this->get('/{cat:site|contact|social}', function ($args) {
                return (new AdminSettingController($this))->showSettings($args);
            })->setName('adminSettings');

            // Save Settings
            $this->post('/save', function ($args) {
                return (new AdminSettingController($this))->saveSettings();
            })->add('csrfGuardHandler')->setName('adminSaveSettings');

            // Show sitemap submit page
            $this->get('/sitemap', function ($args) {
                return (new AdminController($this))->sitemap();
            })->setName('adminSitemap');

            // Update sitemap
            $this->post('/sitemap/update', function ($args) {
                return (new AdminController($this))->updateSitemap();
            })->add('csrfGuardHandler')->setName('adminUpdateSitemap');

            // GitHub release notes
            $this->get('/release/{release:\d+\.\d+\.\d+}', function ($args) {
                return (new AdminController($this))->release($args);
            })->setName('adminEngineRelease');
        });
        // End settings

        // User routes
        $this->group('/user', function () {
            // Show Users
            $this->get('[/]', function ($args) {
                return (new AdminUserController($this))->showUsers();
            })->setName('adminUsers');

            // Save Users
            $this->post('/save', function ($args) {
                return (new AdminUserController($this))->saveUsers();
            })->add('csrfGuardHandler')->setName('adminSaveUsers');

            // Change User Active Status
            $this->post('/changeStatus/{id:[0-9]+}/{status:Y|N}', function ($args) {
                return (new AdminUserController($this))->userStatus($args);
            })->add('csrfGuardHandler')->setName('adminChangeUserStatus');
        });
        // End user routes
    });
    // End tools

    // Help page
    $this->get('/help[/{file}[/{link}]]', function ($args) {
        $args['file'] = $args['file'] ?? 'adminHome';
        return (new AdminController($this))->showHelp($args);
    })->setName('adminHelp');

    // Help get content
    $this->get('/getHelpContent/{file}', function ($args) {
        return (new AdminController($this))->getHelpContent($args);
    })->setName('adminHelpContent');

    // Fallback for when calling /admin to redirect to /admin/home (adminHome)
    $this->get('[/]', function () {
        return $this->response->withRedirect($this->router->pathFor('adminHome'));
    });
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
    // Add http no-cache, no-store headers to prevent back button access to admin
    $response = $next($request, $response);
    return $response->withAddedHeader("Cache-Control", "private, no-cache, no-store, must-revalidate");
});

//
// Public unsecured routes
//

// Login page with form to submit email
$app->get('/login', function ($args) {
    return (new AdminAccessController($this))->showLoginForm();
})->setName('adminLoginForm');

// Accept and validate email, and send login token
$app->post('/requestLoginToken/', function ($args) {
    return (new AdminAccessController($this))->requestLoginToken();
})->add('csrfGuardHandler')->setName('adminRequestLoginToken');

// Accept and validate login token and set session
$app->get('/processLoginToken/{token:[a-zA-Z0-9]{64}}', function ($args) {
    return (new AdminAccessController($this))->processLoginToken($args);
})->setName('adminProcessLoginToken');

// Logout
$app->get('/logout', function ($args) {
    return (new AdminAccessController($this))->logout();
})->setName('adminLogout');
