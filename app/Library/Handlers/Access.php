<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Handlers;

use Piton\Interfaces\SessionInterface;

/**
 * Access Control Handler
 *
 * Manages Authentication and Authorization
 */
class Access
{
    /**
     * Session Handler
     *
     * @var Session Class
     */
    protected $session;

    /**
    * Logged in Key Name
    *
    * @var
    */
    protected $loggedInKey = 'loggedIn';

    /**
     * Constructor
     */
    public function __construct(SessionInterface $sessionHandler)
    {
        $this->session = $sessionHandler;
    }

    /**
     * Is Authenticated
     *
     * Checks if user is currently logged in
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->session->getData($this->loggedInKey);
    }

    /**
     * Start Authenicated Session
     */
    public function startAuthenticatedSession()
    {
        $this->session->setData([$this->loggedInKey => true]);
    }

    /**
     * End Authenticated Session
     */
    public function endAuthenticatedSession()
    {
        $this->session->unsetData($this->loggedInKey);
    }

    /**
     * Generate Login Token Hash
     *
     * Generates login token
     * @return string
     */
    public function generateLoginToken()
    {
        return hash('sha256', microtime() . bin2hex(random_bytes(32)));
    }

    /**
     * Is Authorized
     *
     * Validates that the user has the required role in session
     * @param str Required permission: A: Admin, S: Super Admin
     * @return bool
     */
    public function isAuthorized($requiredRole)
    {
        $userRole = $this->session->getData('role');
        $permissions = ['N' => 1, 'A' => 2, 'S' => 3];

        if (!($requiredRole === 'A' || $requiredRole === 'S')) {
            return false;
        }

        return ($permissions[$requiredRole] <= $permissions[$userRole]);
    }
}
