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
 * Admin User Controller
 *
 * Manage site application users
 */
class AdminUserController extends AdminBaseController
{
    /**
     * Show All Users
     *
     */
    public function showUsers()
    {
        // Get dependencies
        $userMapper = ($this->container->dataMapper)('UserMapper');

        // Fetch users
        $users = $userMapper->find();

        return $this->render('tools/users.html', $users);
    }

    /**
     * Save Users
     *
     * Save all email addresses, ignoring duplicates
     */
    public function saveUsers()
    {
        // Get dependencies
        $userMapper = ($this->container->dataMapper)('UserMapper');
        $users = $this->request->getParsedBodyParam('email');

        // Save users
        foreach ($users as $user) {
            if (!empty($user)) {
                $User = $userMapper->make();
                $User->email = strtolower(trim($user));
                $userMapper->save($User);
            }
        }

        // Redirect back to list of users
        return $this->redirect('adminUsers');
    }

    /**
     * Delete User
     *
     * Delete user email to deny access
     */
    public function deleteUser($args)
    {
        // Get dependencies
        $userMapper = ($this->container->dataMapper)('UserMapper');

        // Delete user
        $User = $userMapper->make();
        $User->id = $args['id'];
        $userMapper->delete($User);

        // Redirect back to list of users
        return $this->redirect('adminUsers');
    }
}
