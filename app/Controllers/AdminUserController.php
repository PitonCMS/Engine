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
     * Save Users
     *
     * Save all users in page
     * @param void
     * @return Response
     */
    public function saveUsers(): Response
    {
        // Get dependencies
        $userMapper = ($this->container->dataMapper)('UserMapper');
        $post = $this->request->getParsedBody();

        // Save users
        foreach ($post['email'] as $key => $row) {
            if (!empty($post['email'][$key])) {
                $user = $userMapper->make();
                $user->id = $post['user_id'][$key];
                $user->role = (isset($post['admin'][$key]) && $post['admin'][$key] === 'on') ? 'A' : null;
                $user->email = strtolower(trim($post['email'][$key]));
                $user->first_name = $post['first_name'][$key];
                $user->last_name = $post['last_name'][$key];
                try {
                    $userMapper->save($user);
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000') {
                        // Duplicate entry error
                        $this->setAlert('danger', 'Duplicate User', "The user {$post['email'][$key]} already exists.");
                        break;
                    }

                    throw $e;
                }
            }
        }

        // Redirect back to list of users
        return $this->redirect('adminToolUser');
    }

    /**
     * Change User Status
     *
     * Sets user active status: Y|N
     * @param array $args
     * @return Response
     */
    public function userStatus($args): Response
    {
        // Get dependencies
        $userMapper = ($this->container->dataMapper)('UserMapper');
        $post = $this->request->getParsedBody();

        // Find user to change status
        foreach ($post['user_id'] as $key => $row) {
            // Both arguments will be strings, so no need to cast one or the other
            if ($row === $args['id']) {
                $user = $userMapper->make();
                $user->id = $post['user_id'][$key];
                $user->active = $args['status'];
                $userMapper->save($user);

                // All done here
                break;
            }
        }

        // Redirect back to list of users
        return $this->redirect('adminToolUser');
    }
}
