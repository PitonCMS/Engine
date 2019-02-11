<?php
/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2019 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */
namespace Piton\Library\Handlers;

use Piton\Interfaces\EmailInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Piton Email Class
 *
 * To use a different email manager class, implement Piton\Interfaces\EmailInterface
 * and override the emailHandler dependency in the container.
 */
class Email implements EmailInterface
{
    /**
     * PHPMailer Object
     * @var PHPMailer\PHPMailer\PHPMailer
     */
    protected $mailer;

    /**
     * Settings Array
     * @var array
     */
    protected $settings;

    /**
     * Logger Object
     * @var object
     */
    protected $logger;

    /**
     * New Function
     *
     * @param
     * @return
     */
    public function __construct($settings, $logger)
    {
        $this->settings = $settings;
        $this->logger = $logger;

        $this->mailer = new PHPMailer(true);
    }

    /**
     * Set From Address
     *
     * @param  string $address From email address
     * @param  string $name    Sender name, optiona
     * @return object $this    Email
     */
    public function setFrom($address, $name = null)
    {
        $this->mailer->setFrom($address, $name, false);

        return $this;
    }

    /**
     * Add Recipient To Address
     *
     * Can be called multiple times to add additional recipients
     * @param  string $address To email address
     * @param  string $name    Recipient name, optiona
     * @return object $this    Email
     */
    public function addTo($address, $name = null)
    {
        $this->mailer->addAddress($address, $name);

        return $this;
    }

    /**
     * Set Email Subject
     *
     * @param  string $subject Email subject line
     * @return object $this    Email
     */
    public function setSubject($subject)
    {
        $this->mailer->Subject =$subject;

        return $this;
    }

    /**
     * Set Email Message Body
     *
     * @param  string $body Email body
     * @return object $this Email
     */
    public function setMessage($message)
    {
        $this->mailer->Body = $message;

        return $this;
    }

    /**
     * Send Email
     *
     * @param  void
     * @return void
     */
    public function send()
    {
        // Has the from address not been set properly? If not, use config default
        if ($this->mailer->From = 'root@localhost' || empty($this->mailer->From)) {
            $this->setFrom($this->settings['email']['from']);
        }

        try {
            $this->mailer->send();
        } catch (Exception $e) {
            // Log for debugging and then rethrow
            $this->logger->critical('PitonCMS: Failed to send mail: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
