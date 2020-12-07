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
 * @package  Kanboard\Core\Mail\Transport
 * @author   Frederic Guillot
 */
class Mail extends Base implements ClientInterface
{
    /**
     * Send a HTML email
     *
     * @access public
     * @param  string $recipientEmail
     * @param  string $recipientName
     * @param  string $subject
     * @param  string $html
     * @param  string $authorName
     * @param  string $authorEmail
     */
    public function sendEmail($recipientEmail, $recipientName, $subject, $html, $authorName, $authorEmail = '')
    {
        try {
            $message = Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($this->helper->mail->getMailSenderAddress(), $authorName)
                ->setTo(array($recipientEmail => $recipientName));

            if (! empty(MAIL_BCC)) {
                $message->setBcc(MAIL_BCC);
            }

            $headers = $message->getHeaders();

            // See https://tools.ietf.org/html/rfc3834#section-5
            $headers->addTextHeader('Auto-Submitted', 'auto-generated');

            if (! empty($authorEmail)) {
                $message->setReplyTo($authorEmail);
            }

            $message->setBody($html, 'text/html');

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
