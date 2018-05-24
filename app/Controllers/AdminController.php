<?php
/**
 * Admin Controller
 */
namespace Piton\Controllers;

class AdminController extends BaseController
{
    /**
     * Admin Home Page
     *
     */
    public function home($request, $response, $args)
    {
        return $this->container->view->render($response, '@admin/home.html', ['page' => $page]);
    }

    /**
     * Show All Users
     *
     */
    public function showUsers($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $UserMapper = $mapper('UserMapper');

        // Fetch users
        $users = $UserMapper->find();

        return $this->container->view->render($response, '@admin/users.html', ['users' => $users]);
    }

    /**
     * Save Users
     *
     * Save all email addresses, ignoring duplicates
     */
    public function saveUsers($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $UserMapper = $mapper('UserMapper');
        $users = $request->getParsedBodyParam('email');

        // Save users
        foreach ($users as $user) {
            if (!empty($user)) {
                $User = $UserMapper->make();
                $User->email = strtolower(trim($user));
                $UserMapper->save($User);
            }
        }

        // Redirect back to list of users
        return $response->withRedirect($this->container->router->pathFor('showUsers'));
    }

    /**
     * Remove User
     *
     * Remove user email to deny access
     */
    public function removeUser($request, $response, $args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $UserMapper = $mapper('UserMapper');

        // Delete user
        $User = $UserMapper->make();
        $User->id = $args['id'];
        $UserMapper->delete($User);

        // Redirect back to list of users
        return $response->withRedirect($this->container->router->pathFor('showUsers'));
    }
}
