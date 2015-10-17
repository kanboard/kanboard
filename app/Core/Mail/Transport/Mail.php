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
                ->setFrom(array(MAIL_FROM => $author))
                ->setBody($html, 'text/html')
                ->setTo(array($email => $name));

            Swift_Mailer::newInstance($this->getTransport())->send($message);
        } catch (Swift_TransportException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Get SwiftMailer transport
     *
     * @access protected
     * @return \Swift_Transport
     */
    protected function getTransport()
    {
        return Swift_MailTransport::newInstance();
    }
}
