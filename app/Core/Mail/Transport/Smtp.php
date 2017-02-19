<?php

namespace Kanboard\Core\Mail\Transport;

use Swift_SmtpTransport;

/**
 * PHP Mail Handler
 *
 * @package  Kanboard\Core\Mail\Transport
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

        if (HTTP_VERIFY_SSL_CERTIFICATE === false) {
            $transport->setStreamOptions(array(
                'ssl' => array(
                    'allow_self_signed' => true,
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                )
            ));
        }

        return $transport;
    }
}
