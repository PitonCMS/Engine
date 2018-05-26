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

    // Show Users
    $this->get('/users', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminController($this))->showUsers($request, $response, $args);
    })->setName('showUsers');

    // Save Users
    $this->post('/saveusers', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminController($this))->saveUsers($request, $response, $args);
    })->setName('saveUsers');

    // Delete User
    $this->get('/deleteuser/{id:[0-9]{1,}}', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminController($this))->deleteUser($request, $response, $args);
    })->setName('deleteUser');

    // Show All Pages
    $this->get('/pages', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->showPages($request, $response, $args);
    })->setName('showPages');

    // Edit Page, or Create Page
    $this->get('/editpage[/{id:[0-9]{0,}}]', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->editPage($request, $response, $args);
    })->setName('editPage');

    // Save Page
    $this->post('/savepage', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->savePage($request, $response, $args);
    })->setName('savePage');

    // Delete Page
    $this->get('/deletepage/{id:[0-9]{0,}}', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->deletePage($request, $response, $args);
    })->setName('deletePage');

    // Edit Page Element, or Create Page Element
    $this->get('/editpageelement[/{id:[0-9]{0,}}]', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->editPageElement($request, $response, $args);
    })->setName('editPageElement');

    // Save Page Element
    $this->post('/savepageelement', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->savePageElement($request, $response, $args);
    })->setName('savePageElement');

    // Delete Page ELement
    $this->get('/deletepageelement/{id:[0-9]{0,}}', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminPageController($this))->deletePageElement($request, $response, $args);
    })->setName('deletePageElement');

    // Show Settings
    $this->get('/settings', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminController($this))->showSettings($request, $response, $args);
    })->setName('showSettings');

    // Save Settings
    $this->post('/savesettings', function ($request, $response, $args) {
        return (new Piton\Controllers\AdminController($this))->saveSettings($request, $response, $args);
    })->setName('saveSettings');
})->add(function ($request, $response, $next) {
    // Authentication
    $security = $this->securityHandler;

    if (!$security->isAuthenticated()) {
        // Failed authentication, redirect away
        $notFound = $this->notFoundHandler;

        return $notFound($request, $response);
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
