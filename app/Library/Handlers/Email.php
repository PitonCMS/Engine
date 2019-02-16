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
     * Mailer
     * @var object PHPMailer\PHPMailer\PHPMailer
     */
    protected $mailer;

    /**
     * Logger Object
     * @var object
     */
    protected $logger;

    /**
     * Settings Array
     * @var array
     */
    protected $settings;

    /**
     * Constructor
     *
     * @param  object $mailer   PHPMailer
     * @param  object $logger   Logging object
     * @param  array  $settings Array of configuration settings
     * @return void
     */
    public function __construct(PHPMailer $mailer, $logger, $settings)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->settings = $settings;

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
     * @return object  $this    EmailInterface
     */
    public function setFrom($address, $name = null) : EmailInterface
    {
        // When using mail/sendmail, we need to set the PHPMailer "auto" flag to false
        // https://github.com/PHPMailer/PHPMailer/issues/1634
        $this->mailer->setFrom($address, $name, false);

        return $this;
    }

    /**
     * Set Recipient To Address
     *
     * Can be called multiple times to add additional recipients
     * @param  string $address To email address
     * @param  string $name    Recipient name, optiona
     * @return object $this    EmailInterface
     */
    public function setTo($address, $name = null) : EmailInterface
    {
        $this->mailer->addAddress($address, $name);

        return $this;
    }

    /**
     * Set Email Subject
     *
     * @param  string $subject Email subject line
     * @return object $this    EmailInterface
     */
    public function setSubject($subject) : EmailInterface
    {
        $this->mailer->Subject = $subject;

        return $this;
    }

    /**
     * Set Email Message Body
     *
     * @param  string $body Email body
     * @return object $this EmailInterface
     */
    public function setMessage($message) : EmailInterface
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
    public function send() : void
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

    /**
     * Configure SMTP
     *
     * All values are derived from configuration settings set in constructor
     * @param  void
     * @return void
     */
    public function configSMTP() : void
    {
        $this->mailer->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->mailer->SMTPDebug = ($this->settings['production']) ? 0 : 2;
        $this->mailer->Host = $this->settings['email']['smtpHost'];
        $this->mailer->Port = $this->settings['email']['smtpPort'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->SMTPSecure = 'ssl';
        $this->mailer->Username = $this->settings['email']['smtpUser'];
        $this->mailer->Password = $this->settings['email']['smtpPass'];
    }
}
