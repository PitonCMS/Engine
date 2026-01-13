<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

use Piton\Controllers\AdminAccessController;
use Piton\Controllers\AdminController;
use Piton\Controllers\AdminMediaController;
use Piton\Controllers\AdminMessageController;
use Piton\Controllers\AdminNavigationController;
use Piton\Controllers\AdminPageController;
use Piton\Controllers\AdminSettingController;
use Piton\Controllers\AdminUserController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;

//
// Private secured routes
//
$app->group('/admin', function (RouteCollectorProxy $app) {

    // Admin home
    $app->get('/home', function ($request, $response, $args) {
        return (new AdminController($request, $response, $this))->home();
    })->setName('adminHome');

    // Page route group
    $app->group('/page', function (RouteCollectorProxy $app) {
        // XHR: Get page list asynchronously
        $app->get('/get', function ($request, $response, $args) {
            return (new AdminPageController($request, $response, $this))->getPages();
        })->setName('adminPageGet');

        // Edit or add new page. Must provide ID or page layout argument
        $app->get('/edit[/[{id:[0-9]+}]]', function ($request, $response, $args) {
            $args['type'] = 'page';

            return (new AdminPageController($request, $response, $this))->editPage($args);
        })->setName('adminPageEdit');

        // Save Page for Update or Insert
        $app->post('/save', function ($request, $response, $args) {
            return (new AdminPageController($request, $response, $this))->savePage();
        })->add('csrfGuardHandler')->setName('adminPageSave');

        // Delete page
        $app->post('/delete', function ($request, $response, $args) {
            return (new AdminPageController($request, $response, $this))->deletePage($args);
        })->add('csrfGuardHandler')->setName('adminPageDelete');

        // Page elements group
        $app->group('/element', function (RouteCollectorProxy $app) {
            // XHR: Get element
            $app->get('/get', function ($request, $response, $args) {
                return (new AdminPageController($request, $response, $this))->getNewElement();
            })->setName('adminPageElementGet');

            // XHR: Delete ELement
            $app->post('/delete', function ($request, $response, $args) {
                return (new AdminPageController($request, $response, $this))->deleteElement();
            })->add('csrfGuardHandler')->setName('adminPageElementDelete');
        });

        // Show All Pages
        $app->get('[/]', function ($request, $response, $args) {
            return (new AdminPageController($request, $response, $this))->showPages();
        })->setName('adminPage');

        // End page elements
    });
    // End page routes

    // Collection route group
    $app->group('/collection', function (RouteCollectorProxy $app) {
        // Edit collection
        $app->get('/edit[/[{id:[0-9]+}]]', function ($request, $response, $args) {
            return (new AdminPageController($request, $response, $this))->editCollection($args);
        })->setName('adminCollectionEdit');

        // Save collection
        $app->post('/save', function ($request, $response, $args) {
            return (new AdminPageController($request, $response, $this))->saveCollection();
        })->add('csrfGuardHandler')->setName('adminCollectionSave');

        // Delete collection
        $app->post('/delete', function ($request, $response, $args) {
            return (new AdminPageController($request, $response, $this))->deleteCollection();
        })->add('csrfGuardHandler')->setName('adminCollectionDelete');

        // Show all collection, filtered optionally by collection
        $app->get('[/]', function ($request, $response, $args) {
            return (new AdminPageController($request, $response, $this))->showCollectionGroups();
        })->setName('adminCollection');
    });
    // End collection

    // Navigation route group
    $app->group('/navigation', function (RouteCollectorProxy $app) {
        // Show Navigators
        $app->get('[/]', function ($request, $response, $args) {
            return (new AdminNavigationController($request, $response, $this))->showNavigators();
        })->setName('adminNavigation');

        // Save Navigation
        $app->post('/save', function ($request, $response, $args) {
            return (new AdminNavigationController($request, $response, $this))->saveNavigation();
        })->add('csrfGuardHandler')->setName('adminNavigationSave');

        // Edit Navigator
        $app->get('/edit/{navigator:[a-zA-Z0-9-]+}', function ($request, $response, $args) {
            return (new AdminNavigationController($request, $response, $this))->editNavigator($args);
        })->setName('adminNavigationEdit');

        // XHR: Delete navigation
        $app->post('/delete', function ($request, $response, $args) {
            return (new AdminNavigationController($request, $response, $this))->deleteNavigator();
        })->add('csrfGuardHandler')->setName('adminNavigationDelete');
    });
    // End Navigation

    // Media route group
    $app->group('/media', function (RouteCollectorProxy $app) {
        // XHR: Get media asynchronously
        $app->get('/get/[{context:edit|static}]', function ($request, $response, $args) {
            return (new AdminMediaController($request, $response, $this))->getMedia($args);
        })->setName('adminMediaGet');

        // XHR: Get media controls asynchronously
        $app->get('/getmediacontrols', function ($request, $response, $args) {
            return (new AdminMediaController($request, $response, $this))->getMediaSearchControls();
        })->setName('adminMediaControlsGet');

        // XHR: Get media file upload form asynchronously
        $app->get('/uploadform', function ($request, $response, $args) {
            return (new AdminMediaController($request, $response, $this))->getMediaUploadForm();
        })->setName('adminMediaUploadFormGet');

        // XHR: File upload
        $app->post('/upload', function ($request, $response, $args) {
            return (new AdminMediaController($request, $response, $this))->uploadMedia();
        })->add('csrfGuardHandler')->setName('adminMediaUploadFile');

        // XHR: Media save
        $app->post('/save', function ($request, $response, $args) {
            return (new AdminMediaController($request, $response, $this))->saveMedia();
        })->add('csrfGuardHandler')->setName('adminMediaSave');

        // XHR: Media delete
        $app->post('/delete', function ($request, $response, $args) {
            return (new AdminMediaController($request, $response, $this))->deleteMedia();
        })->add('csrfGuardHandler')->setName('adminMediaDelete');

        // Media route group
        $app->group('/category', function (RouteCollectorProxy $app) {
            $app->get('/edit', function ($request, $response, $args) {
                return (new AdminMediaController($request, $response, $this))->editMediaCategories();
            })->setName('adminMediaCategoryEdit');

            // Save media category
            $app->post('/save', function ($request, $response, $args) {
                return (new AdminMediaController($request, $response, $this))->saveMediaCategories();
            })->add('csrfGuardHandler')->setName('adminMediaCategorySave');

            // XHR Delete media category
            $app->post('/delete', function ($request, $response, $args) {
                return (new AdminMediaController($request, $response, $this))->deleteMediaCategory();
            })->add('csrfGuardHandler')->setName('adminMediaCategoryDelete');

            // XHR Save Category Sort Order
            $app->post('/saveorder', function ($request, $response, $args) {
                return (new AdminMediaController($request, $response, $this))->saveCategoryMediaOrder();
            })->add('csrfGuardHandler')->setName('adminMediaCategorySaveOrder');
        });

        // Show all media
        $app->get('[/]', function ($request, $response, $args) {
            return (new AdminMediaController($request, $response, $this))->showMedia();
        })->setName('adminMedia');
    });
    // End media

    // Messages route group
    $app->group('/message', function (RouteCollectorProxy $app) {
        // Show message page
        $app->get('[/]', function ($request, $response, $args) {
            return (new AdminMessageController($request, $response, $this))->showMessages();
        })->setName('adminMessage');

        // XHR: Get filtered messages
        $app->get('/get', function ($request, $response, $args) {
            return (new AdminMessageController($request, $response, $this))->getMessages();
        })->setName('adminMessageGet');

        // XHR: Get new message count
        $app->get('/getnewmessagecount', function ($request, $response, $args) {
            return (new AdminMessageController($request, $response, $this))->getNewMessageCount();
        })->setName('adminMessageCountGet');

        // XHR: Save message status changes, Archvie, Read, and Delete
        $app->post('/save', function ($request, $response, $args) {
            return (new AdminMessageController($request, $response, $this))->updateStatus();
        })->add('csrfGuardHandler')->setName('adminMessageSave');
    });
    // End messages

    // Settings route group
    $app->group('/settings', function (RouteCollectorProxy $app) {
        // Show settings landing page
        $app->get('[/]', function ($request, $response, $args) {
            return (new AdminSettingController($request, $response, $this))->showSettings($args);
        })->setName('adminSetting');

        // Save settings
        $app->post('/save', function ($request, $response, $args) {
            return (new AdminSettingController($request, $response, $this))->saveSettings();
        })->add('csrfGuardHandler')->setName('adminSettingSave');

        // Show sitemap submit page
        $app->get('/sitemap', function ($request, $response, $args) {
            return (new AdminController($request, $response, $this))->sitemap();
        })->setName('adminSitemap');

        // Update sitemap
        $app->post('/sitemap/update', function ($request, $response, $args) {
            return (new AdminController($request, $response, $this))->updateSitemap();
        })->add('csrfGuardHandler')->setName('adminSitemapUpdate');

        // Edit settings by category
        $app->get('/{category:site|contact|social}/edit', function ($request, $response, $args) {
            return (new AdminSettingController($request, $response, $this))->editSettings($args);
        })->setName('adminSettingEdit');
    });
    // End settings

    // User route group
    $app->group('/user', function (RouteCollectorProxy $app) {
        // Show Users
        $app->get('[/]', function ($request, $response, $args) {
            return (new AdminUserController($request, $response, $this))->showUsers();
        })->setName('adminUser');

        // Edit User
        $app->get('/edit[/[{id:[0-9]+}]]', function ($request, $response, $args) {
            return (new AdminUserController($request, $response, $this))->editUser($args);
        })->setName('adminUserEdit');

        // Save Users
        $app->post('/save', function ($request, $response, $args) {
            return (new AdminUserController($request, $response, $this))->saveUser();
        })->add('csrfGuardHandler')->setName('adminUserSave');
    });
    // End user routes

    // Support content route group
    $app->group('/support', function (RouteCollectorProxy $app) {
        // About PitonCMS
        $app->get('/about', function ($request, $response, $args) {
            return (new AdminController($request, $response, $this))->aboutPiton($args);
        })->setName('adminSupportAbout');

        // Support index
        $app->get('/{subject:client|designer}', function ($request, $response, $args) {
            return (new AdminController($request, $response, $this))->showSupportIndex($args);
        })->setName('adminSupportIndex');

        // Support content page
        $app->get('/{subject:client|designer}/{file:[a-zA-Z]+}[/{link:[a-zA-Z]+}]', function ($request, $response, $args) {
            return (new AdminController($request, $response, $this))->showSupportContent($args);
        })->setName('adminSupportContent');
    });

    // Fallback for when calling /admin to redirect to /admin/home (adminHome)
    $app->get('[/]', function ($request, $response, $args) {
        return $response->withHeader('Location', $this->get('router')->urlFor('adminHome'))->withStatus(302);
    });
})->add(function (Request $request, RequestHandler $handler) use ($app) {
    // Authentication

    // To bypass authentication on a **NON-PRODUCTION** envornment only, see Support > Designer > Security > #bypass-authentication
    if (
        $this->get('settings')['environment']['production'] === false
        && isset($this->get('settings')['session']['bypassAuthentication'])
        && $this->get('settings')['session']['bypassAuthentication'] === true
    ) {
        // Bypassing authentication, just go to next call
        return $handler->handle($request);
    }

    // Otherwise run authentication
    $security = $this->get('accessHandler');

    if (!$security->isAuthenticated()) {
        // Failed authentication, redirect to login. First create a new Response object and then redirect
        $response = $app->getResponseFactory()->createResponse();

        return $response->withHeader('Location', $this->get('router')->urlFor('adminLoginForm'))->withStatus(302);
    }

    // Next call
    return $handler->handle($request);
})->add(function (Request $request, RequestHandler $handler) {
    // Add http no-cache, no-store headers to prevent back button access to admin
    $response = $handler->handle($request);

    return $response->withAddedHeader('Cache-Control', 'private, no-cache, no-store, must-revalidate');
});

//
// Public unsecured admin routes
//

// Login page with form to submit email
$app->get('/login', function ($request, $response, $args) {
    return (new AdminAccessController($request, $response, $this))->showLoginForm();
})->setName('adminLoginForm');

// Accept and validate email, and send login token
$app->post('/requestLoginToken', function ($request, $response, $args) {
    return (new AdminAccessController($request, $response, $this))->requestLoginToken();
})->add('csrfGuardHandler')->setName('adminRequestLoginToken');

// Accept and validate login token and set session
$app->get('/processLoginToken/{token:[a-zA-Z0-9]{64}}', function ($request, $response, $args) {
    return (new AdminAccessController($request, $response, $this))->processLoginToken($args);
})->setName('adminProcessLoginToken');

// Logout
$app->get('/logout', function ($request, $response, $args) {
    return (new AdminAccessController($request, $response, $this))->logout();
})->setName('adminLogout');
