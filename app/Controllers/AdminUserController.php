<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2020 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use PDOException;
use Throwable;

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
     * @param void
     * @return Response
     */
    public function showUsers(): Response
    {
        // Get dependencies
        $userMapper = ($this->container->dataMapper)('UserMapper');

        // Fetch users
        $data['users'] = $userMapper->findUsers();

        // If there is only one admin, set flag to suggest a recovery email
        $admins = 0;
        foreach ($data['users'] as $user) {
            if ($user->role === 'A') {
                $admins++;
            }
        }
        $data['recommendRecoveryEmail'] = ($admins < 2) ? true : false;

        return $this->render('users/users.html', $data);
    }

    /**
     * Edit User
     *
     * @param  array $args
     * @return Response
     */
    public function editUser($args): Response
    {
        // Get dependencies
        $userMapper = ($this->container->dataMapper)('UserMapper');

        // Fetch user or make new user
        if (isset($args['id'])) {
            $data['user'] = $userMapper->findById((int) $args['id']);
        } else {
            $data['user'] = $userMapper->make();
        }

        return $this->render('users/userEdit.html', $data);
    }

    /**
     * Save User
     *
     * Save all users
     * @param void
     * @return Response
     */
    public function saveUser(): Response
    {
        // Get dependencies
        $userMapper = ($this->container->dataMapper)('UserMapper');
        $session = $this->container->sessionHandler;

        // Save user
        $params = $this->request->getParsedBody();
        $user = $userMapper->make();
        $user->id = empty($params['user_id']) ? null : (int) $params['user_id'];
        $user->first_name = trim($params['first_name']);
        $user->last_name = trim($params['last_name']);
        $user->email = trim($params['email']);
        $user->role = 'A'; // Set to Admin for now (TODO)
        $user->active = ($params['active']) ? 'Y' : 'N';

        try {
            // There might be a duplicate user email
            $user = $userMapper->save($user);

            // If the active status has changed to 'N' then delete any current sessions for that user
            if ($user->active === 'N') {
                $session->deleteSessionsByUserId($user->id);
            }
        } catch (Throwable $e) {
            if ($e->getCode() === '23000') {
                // Duplicate email error
                $this->setAlert('danger', 'Duplicate User Email', "The user email {$user->email} already exists.");

                // Redirect to users. If a new user failed to save the ID will be falsey (0)
                $user->id = $user->id ?: null;
                return $this->redirect('adminUserEdit', ['id' => $user->id]);
            }

            throw $e;
        }

        // Redirect to users
        return $this->redirect('adminUserEdit', ['id' => $user->id]);
    }
}
