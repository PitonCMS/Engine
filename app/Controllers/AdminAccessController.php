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

use Slim\Http\Response;

/**
 * Piton Access Controller
 *
 * Piton uses a passwordless login process, in which a user requests a one-time use login token
 * to be sent by email to the user's validated email account. The login flow is:
 *
 * 1 Render login page form, which accepts an email address
 * 2 Submit (POST) the email, and validate the email string to a list of known privileged users
 * 3 Generate a one-time use hash token, save to session data, and send token in query string to user's email account
 * 4 User opens email, and submits link with token
 * 5 The application validates the submitted token to the one in session data, and if not expired an authenticated
 *      session is started
 */
class AdminAccessController extends AdminBaseController
{
    /**
     * Login Token Key Name
     * @var string
     */
    private $loginTokenKey = 'loginToken';

    /**
     * Login Token Key Expires Name
     * @var string
     */
    private $loginTokenExpiresKey = 'loginTokenExpires';

    /**
     * Show Login Form
     *
     * Render page with form to submit email
     * @param void
     * @return Response
     */
    public function showLoginForm(): Response
    {
        return $this->render('login.html');
    }

    /**
     * Request Login Token
     *
     * Validates email and sends login link to user
     * @param void
     * @return Response
     */
    public function requestLoginToken(): Response
    {
        // Get dependencies
        $session = $this->container->sessionHandler;
        $email = $this->container->emailHandler;
        $security = $this->container->accessHandler;
        $userMapper = ($this->container->dataMapper)('UserMapper');
        $body = $this->request->getParsedBody();

        // Fetch all users
        $userList = $userMapper->findActiveUsers();

        // Clean provided email
        $providedEmail = strtolower(trim($body['email']));

        $foundValidUser = false;
        foreach ($userList as $user) {
            if ($user->email === $providedEmail) {
                $foundValidUser = $user;
                break;
            }
        }

        // Did we find a match?
        if (!$foundValidUser) {
            // No, log and silently redirect to home
            $this->container->logger->alert('Failed login attempt: ' . $body['email']);

            return $this->redirect('home');
        }

        // Belt and braces/suspenders double check
        if ($foundValidUser->email === $providedEmail) {
            // Get and set token, and user ID
            $token = $security->generateLoginToken();
            $session->setData([
                $this->loginTokenKey => $token,
                $this->loginTokenExpiresKey => time() + 120,
                'user_id' => $foundValidUser->id,
                'email' => $foundValidUser->email
            ]);

            // Get request details to create login link and email to user
            $link = $this->request->getUri()->getBaseUrl();
            $link .= $this->container->router->pathFor('adminProcessLoginToken', ['token' => $token]);

            // Send message
            $email->setTo($providedEmail, '')
                ->setSubject('PitonCMS Login')
                ->setMessage("Click to login\n\n $link")
                ->send();
        }

        // Direct to home page
        return $this->redirect('home');
    }

    /**
     * Process Login Token
     *
     * Validate login token and authenticate request
     * @param array
     * @return Response
     */
    public function processLoginToken(array $args): Response
    {
        // Get dependencies
        $session = $this->container->sessionHandler;
        $security = $this->container->accessHandler;
        $savedToken = $session->getData($this->loginTokenKey);
        $tokenExpires = (int) $session->getData($this->loginTokenExpiresKey);

        // Checks whether token matches, and if within expires time
        if ($args['token'] === $savedToken && time() < $tokenExpires) {
            // Successful, set session
            $security->startAuthenticatedSession();

            // Delete token
            $session->unsetData($this->loginTokenKey);
            $session->unsetData($this->loginTokenExpiresKey);

            // Go to admin dashboard
            return $this->redirect('adminHome');
        }

        // Not valid, direct home
        $message = $args['token'] . ' saved: ' . $savedToken . ' time: ' . time() . ' expires: ' . $tokenExpires;
        $this->container->logger->info('Invalid login token, supplied: ' . $message);

        return $this->notFound();
    }

    /**
     * Logout
     *
     * Unsets logged in status
     * @param void
     * @return Response
     */
    public function logout(): Response
    {
        // Unset authenticated session
        $security = $this->container->accessHandler;
        $security->endAuthenticatedSession();

        // Unset CSRF Token
        $csrfGuard = $this->container->csrfGuard;
        $csrfGuard->unsetToken();

        return $this->redirect('home');
    }
}
