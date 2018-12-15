<?php
/**
 * Administration Application Routes
 */

//
// Private secured routes
//

$app->group('/admin', function () {

    // Admin home
    $this->get('/home', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminController($this))->home($request, $response, $args);
    })->setName('adminHome');

    // User routes
    $this->group('/user', function () {
        // Show Users
        $this->get('[/]', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminUserController($this))->showUsers($request, $response, $args);
        })->setName('showUsers');

        // Save Users
        $this->post('/save', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminUserController($this))->saveUsers($request, $response, $args);
        })->setName('saveUsers');

        // Delete User
        $this->get('/delete/{id:[0-9]{1,}}', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminUserController($this))->deleteUser($request, $response, $args);
        })->setName('deleteUser');
    });
    // End user routes

    // Page route
    $this->group('/page', function () {
        // Show All Pages
        $this->get('[/]', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminPageController($this))->showPages($request, $response, $args);
        })->setName('showPages');

        // Edit Page, or Create New Page
        $this->get('/edit[/{id}]', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminPageController($this))->editPage($request, $response, $args);
        })->setName('editPage');

        // Save Page
        $this->post('/save', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminPageController($this))->savePage($request, $response, $args);
        })->setName('savePage');

        // Delete Page
        $this->get('/delete/{id:[0-9]{0,}}', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminPageController($this))->deletePage($request, $response, $args);
        })->setName('deletePage');

        // Page Section
        $this->group('/section', function () {
            // Delete Section ELement
            $this->get('/deleteelement/{id:[0-9]{0,}}', function ($request, $response, $args) {
                return (new Piton\Controllers\AdminPageController($this))->deletePageSectionElement($request, $response, $args);
            });
        });
        // End page section

        // Page elements
        $this->group('/element', function () {
            // Fetch element form
            $this->post('/fetch', function ($request, $response, $args) {
                return (new Piton\Controllers\AdminPageController($this))->fetchElementForm($request, $response, $args);
            });
        });
        // End page elements
    });
    // End page routes

    // Settings
    $this->group('/settings', function () {
        // Show Settings
        $this->get('[/]', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminSettingController($this))->showSettings($request, $response, $args);
        })->setName('showSettings');

        // Save Settings
        $this->post('/save', function ($request, $response, $args) {
            return (new Piton\Controllers\AdminSettingController($this))->saveSettings($request, $response, $args);
        })->setName('saveSettings');
    });
    // End settings
})->add(function ($request, $response, $next) {
    // Authentication
    $security = $this->accessHandler;

    if (!$security->isAuthenticated()) {
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
$app->get('/letmein', function ($request, $response, $args) {
    return (new Piton\Controllers\LoginController($this))->showLoginForm($request, $response, $args);
})->setName('showLoginForm');

// Accept and validate email, and send login token
$app->post('/requestlogintoken/', function ($request, $response, $args) {
    return (new Piton\Controllers\LoginController($this))->requestLoginToken($request, $response, $args);
})->setName('requestLoginToken');

// Accept and validate login token and set session
$app->get('/processlogintoken/{token:[a-zA-Z0-9]{64}}', function ($request, $response, $args) {
    return (new Piton\Controllers\LoginController($this))->processLoginToken($request, $response, $args);
})->setName('processLoginToken');

// Logout
$app->get('/logout', function ($request, $response, $args) {
    return (new Piton\Controllers\LoginController($this))->logout($request, $response, $args);
})->setName('logout');
