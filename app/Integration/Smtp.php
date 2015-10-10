<?php

namespace Integration;

use Swift_Message;
use Swift_Mailer;
use Swift_MailTransport;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Swift_TransportException;

/**
 * Smtp
 *
 * @package  integration
 * @author   Frederic Guillot
 */
class Smtp extends \Core\Base
{
    /**
     * Send a HTML email
     *
     * @access public
     * @param  string  $email
     * @param  string  $name
     * @param  string  $subject
     * @param  string  $html
     * @param  string  $author
     */
    public function sendEmail($email, $name, $subject, $html, $author)
    {
        try {

            $message = Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom(array(MAIL_FROM => $author))
                ->setBody($html, 'text/html')
                ->setTo(array($email => $name));

            Swift_Mailer::newInstance($this->getTransport())->send($message);
        }
        catch (Swift_TransportException $e) {
            $this->container['logger']->error($e->getMessage());
        }
    }

    /**
     * Get SwiftMailer transport
     *
     * @access private
     * @return \Swift_Transport
     */
    private function getTransport()
    {
        switch (MAIL_TRANSPORT) {
            case 'smtp':
                $transport = Swift_SmtpTransport::newInstance(MAIL_SMTP_HOSTNAME, MAIL_SMTP_PORT);
                $transport->setUsername(MAIL_SMTP_USERNAME);
                $transport->setPassword(MAIL_SMTP_PASSWORD);
                $transport->setEncryption(MAIL_SMTP_ENCRYPTION);
                break;
            case 'sendmail':
                $transport = Swift_SendmailTransport::newInstance(MAIL_SENDMAIL_COMMAND);
                break;
            default:
                $transport = Swift_MailTransport::newInstance();
        }

        return $transport;
    }
}
