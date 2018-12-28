<?php
/**
 * Administration Application Routes
 */
use Piton\Controllers\AdminController;
use Piton\Controllers\AdminUserController;
use Piton\Controllers\AdminPageController;
use Piton\Controllers\AdminSettingController;
use Piton\Controllers\AccessController;

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
        })->setName('saveUsers');

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

        // Edit Page, or Create New Page
        $this->get('/edit[/{id}]', function ($args) {
            return (new AdminPageController($this))->editPage($args);
        })->setName('editPage');

        // Save Page
        $this->post('/save', function ($args) {
            return (new AdminPageController($this))->savePage();
        })->setName('savePage');

        // Delete Page
        $this->get('/delete/{id:[0-9]{0,}}', function ($args) {
            return (new AdminPageController($this))->deletePage($args);
        })->setName('deletePage');

        // Page elements
        $this->group('/element', function () {
            // Fetch element form
            $this->post('/new', function ($args) {
                return (new AdminPageController($this))->newElementForm();
            });

            // Delete ELement
            $this->get('/delete/{id:[0-9]{0,}}', function ($args) {
                return (new AdminPageController($this))->deleteElement($args);
            });
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
        })->setName('saveSettings');

        // Show Custom Settings
        $this->get('/custom', function ($args) {
            return (new AdminSettingController($this))->showCustomSettings();
        })->setName('showCustomSettings');

        // Add Custom Setting
        $this->get('/custom/new', function ($args) {
            return (new AdminSettingController($this))->newCustomSettingForm();
        });

        // Save Custom Settings
        $this->post('/custom/save', function ($args) {
            return (new AdminSettingController($this))->saveCustomSettings();
        })->setName('saveCustomSettings');

        // Delete Custom Setting
        $this->get('/custom/delete/{id:[0-9]{0,}}', function ($args) {
            return (new AdminSettingController($this))->deleteCustomSettings($args);
        })->setName('deleteCustomSetting');
    });
    // End settings
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
    return (new AccessController($this))->showLoginForm();
})->setName('showLoginForm');

// Accept and validate email, and send login token
$app->post('/requestlogintoken/', function ($args) {
    return (new AccessController($this))->requestLoginToken();
})->setName('requestLoginToken');

// Accept and validate login token and set session
$app->get('/processlogintoken/{token:[a-zA-Z0-9]{64}}', function ($args) {
    return (new AccessController($this))->processLoginToken($args);
})->setName('processLoginToken');

// Logout
$app->get('/logout', function ($args) {
    return (new AccessController($this))->logout();
})->setName('logout');
