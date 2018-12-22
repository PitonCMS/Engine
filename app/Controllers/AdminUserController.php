<?php
/**
 * Admin User Controller
 *
 * Manage site application users
 */
namespace Piton\Controllers;

class AdminUserController extends AdminBaseController
{
    /**
     * Show All Users
     *
     */
    public function showUsers()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $UserMapper = $mapper('UserMapper');

        // Fetch users
        $users = $UserMapper->find();

        return $this->render('showUsers.html', ['users' => $users]);
    }

    /**
     * Save Users
     *
     * Save all email addresses, ignoring duplicates
     */
    public function saveUsers()
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $UserMapper = $mapper('UserMapper');
        $users = $this->request->getParsedBodyParam('email');

        // Save users
        foreach ($users as $user) {
            if (!empty($user)) {
                $User = $UserMapper->make();
                $User->email = strtolower(trim($user));
                $UserMapper->save($User);
            }
        }

        // Redirect back to list of users
        return $this->redirect('showUsers');
    }

    /**
     * Delete User
     *
     * Delete user email to deny access
     */
    public function deleteUser($args)
    {
        // Get dependencies
        $mapper = $this->container->dataMapper;
        $UserMapper = $mapper('UserMapper');

        // Delete user
        $User = $UserMapper->make();
        $User->id = $args['id'];
        $UserMapper->delete($User);

        // Redirect back to list of users
        return $this->redirect('showUsers');
    }
}
