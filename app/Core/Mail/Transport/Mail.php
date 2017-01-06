<?php

namespace Kanboard\Core\Mail\Transport;

use Swift_Message;
use Swift_Mailer;
use Swift_MailTransport;
use Swift_TransportException;
use Kanboard\Core\Base;
use Kanboard\Core\Mail\ClientInterface;

/**
 * PHP Mail Handler
 *
 * @package  transport
 * @author   Frederic Guillot
 */
class Mail extends Base implements ClientInterface
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
                ->setFrom(array($this->helper->mail->getMailSenderAddress() => $author))
                ->setTo(array($email => $name))
                ->setBody($html, 'text/html');

            Swift_Mailer::newInstance($this->getTransport())->send($message);
        } catch (Swift_TransportException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Get SwiftMailer transport
     *
     * @access protected
     * @return \Swift_Transport|\Swift_MailTransport|\Swift_SmtpTransport|\Swift_SendmailTransport
     */
    protected function getTransport()
    {
        return Swift_MailTransport::newInstance();
    }
}
