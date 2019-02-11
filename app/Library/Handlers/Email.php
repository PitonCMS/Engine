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
class Email extends PHPMailer implements EmailInterface
{
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
     * Constructor
     *
     * @param  array  $settings Array of configuration settings
     * @param  object $logger Logging object
     * @return void
     */
    public function __construct($settings, $logger)
    {
        $this->settings = $settings;
        $this->logger = $logger;

        // Enable exceptions in PHPMailer
        parent::__construct(true);

        // Check if a SMTP connection was requested and then configure
        if (strtolower($this->settings['email']['protocol']) === 'smtp') {
            $this->configSMTP();
        }
    }

    /**
     * Set From Address
     *
     * @param  string  $address From email address
     * @param  string  $name    Sender name, optional
     * @param  boolean $auto    NOT USED
     * @return object  $this    Email
     */
    public function setFrom($address, $name = '', $auto = true)
    {
        // When using mail/sendmail, we need to set the PHPMailer "auto" flag to false
        // https://github.com/PHPMailer/PHPMailer/issues/1634
        parent::setFrom($address, $name, false);

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
        $this->addAddress($address, $name);

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
        $this->Subject =$subject;

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
        $this->Body = $message;

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
        if ($this->From = 'root@localhost' || empty($this->From)) {
            $this->setFrom($this->settings['email']['from']);
        }

        try {
            parent::send();
        } catch (Exception $e) {
            // Log for debugging and then rethrow
            $this->logger->critical('PitonCMS: Failed to send mail: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Configure SMTP
     *
     * All values are derived from configuration settings set in constructor
     * @param  void
     * @return void
     */
    public function configSMTP()
    {
        $this->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->SMTPDebug = ($this->settings['production']) ? 2 : 0;
        $this->Host = $this->settings['email']['smtpHost'];
        $this->Port = $this->settings['email']['smtpPort'];
        $this->SMTPAuth = true;
        $this->SMTPSecure = 'ssl';
        $this->Username = $this->settings['email']['smtpUser'];
        $this->Password = $this->settings['email']['smtpPass'];
    }
}
