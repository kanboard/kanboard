<?php

namespace Kanboard\Core\Mail\Transport;

use Swift_SmtpTransport;

/**
 * PHP Mail Handler
 *
 * @package  transport
 * @author   Frederic Guillot
 */
class Smtp extends Mail
{
    /**
     * Get SwiftMailer transport
     *
     * @access protected
     * @return \Swift_Transport
     */
    protected function getTransport()
    {
        $transport = Swift_SmtpTransport::newInstance(MAIL_SMTP_HOSTNAME, MAIL_SMTP_PORT);
        $transport->setUsername(MAIL_SMTP_USERNAME);
        $transport->setPassword(MAIL_SMTP_PASSWORD);
        $transport->setEncryption(MAIL_SMTP_ENCRYPTION);

        return $transport;
    }
}
