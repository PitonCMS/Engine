<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use PDOException;

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

        return $this->render('tools/users.html', $data);
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

        return $this->render('tools/userEdit.html', $data);
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
        $id = (int) $this->request->getParsedBodyParam('user_id');

        // Save user
        $user = $userMapper->make();
        $user->id = $id;
        $user->first_name = trim($this->request->getParsedBodyParam('first_name'));
        $user->last_name = trim($this->request->getParsedBodyParam('last_name'));
        $user->email = trim($this->request->getParsedBodyParam('email'));

        $user->role = ($this->request->getParsedBodyParam('role')) ? 'A' : null;
        $user->active = ($this->request->getParsedBodyParam('active')) ? 'Y' : 'N';

        try {
            // There might be a duplicate user email
            $userMapper->save($user);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                // Duplicate email_uq unique index error
                $this->setAlert('danger', 'Duplicate Email', "The user email {$row['email']} already exists.");

                return $this->redirect('adminToolUserEdit', ['id' => $id]);
            }

            throw $e;
        }

        // Redirect to users
        return $this->redirect('adminToolUser');
    }
}
