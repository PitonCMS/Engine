<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2026 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.
 */

declare(strict_types=1);

namespace Piton\Library\Handlers;

use PHPMailer\PHPMailer\PHPMailer;
use Piton\Library\Config;
use Piton\Library\Interfaces\EmailInterface;
use Psr\Log\LoggerInterface as Logger;

/**
 * Piton Email Class
 *
 * To use a different email manager class, implement Piton\Library\Interfaces\EmailInterface
 * and override the emailHandler dependency in the container.
 */
class Email implements EmailInterface
{
    /**
     * Mailer
     * @var object PHPMailer\PHPMailer\PHPMailer
     */
    protected PHPMailer $mailer;

    /**
     * Logger Object
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Config Settings
     * @var Config
     */
    protected Config $settings;

    /**
     * Constructor
     *
     * @param  PHPMailer $mailer   PHPMailer
     * @param  Logger    $logger   Logging object
     * @param  Config    $settings Configuration settings
     */
    public function __construct(PHPMailer $mailer, Logger $logger, Config $settings)
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
     * Set Reply To Address
     *
     * Set the Reply To email address, which is different from the server From address.
     * @param  string  $address Reply To email address
     * @param  ?string  $name    Sender name, optional
     * @return EmailInterface  $this
     */
    public function setReplyTo(string $address, ?string $name = null): EmailInterface
    {
        $this->mailer->addReplyTo($address, $name);

        return $this;
    }

    /**
     * Set Recipient To Address
     *
     * Can be called multiple times to add additional recipients
     * @param  string $address To email address
     * @param  ?string $name    Recipient name, optiona
     * @return EmailInterface $this
     */
    public function setTo(string $address, ?string $name = null): EmailInterface
    {
        $this->mailer->addAddress($address, $name);

        return $this;
    }

    /**
     * Set Email Subject
     *
     * @param  string $subject Email subject line
     * @return EmailInterface $this
     */
    public function setSubject(string $subject): EmailInterface
    {
        $this->mailer->Subject = $subject;

        return $this;
    }

    /**
     * Set Email Message Body
     *
     * @param  string $body Email body
     * @return EmailInterface $this
     */
    public function setMessage(string $message): EmailInterface
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
    public function send(): void
    {
        // Has the from address not been set properly? If not, use config default
        if ($this->mailer->From = 'root@localhost' || empty($this->mailer->From)) {
            // When using mail/sendmail, we need to set the PHPMailer "auto" flag to false
            // https://github.com/PHPMailer/PHPMailer/issues/1634
            $this->mailer->setFrom($this->settings['email']['from'], '', false);
        }

        try {
            $this->mailer->send();
        } catch (\Throwable $e) {
            // Log for debugging and then rethrow
            $this->logger->error('PitonCMS: Failed to send mail: ' . $e->getMessage());

            throw new \Throwable($e->getMessage());
        }
    }

    /**
     * Configure SMTP
     *
     * All values are derived from configuration settings set in constructor
     * @param  void
     * @return void
     */
    public function configSMTP(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->SMTPDebug = 0;
        $this->mailer->Host = $this->settings['email']['smtpHost'];
        $this->mailer->Port = $this->settings['email']['smtpPort'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mailer->Username = $this->settings['email']['smtpUser'];
        $this->mailer->Password = $this->settings['email']['smtpPass'];
    }
}
