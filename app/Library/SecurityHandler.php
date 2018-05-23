<?php
/**
 * Security Handler
 *
 * Manages Authorization and Authentication
 */
namespace Piton\Library;

class SecurityHandler
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
    public function __construct($sessionHandler)
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
        $this->session->destroy();
    }

    /**
     * Generate Login Token Hash
     *
     * Generates login token
     * @return string
     */
    public function generateLoginToken()
    {
        return hash('sha256', microtime() . mt_rand());
    }
}
