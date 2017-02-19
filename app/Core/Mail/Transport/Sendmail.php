<?php

namespace Kanboard\Core\Mail\Transport;

use Swift_SendmailTransport;

/**
 * PHP Mail Handler
 *
 * @package  Kanboard\Core\Mail\Transport
 * @author   Frederic Guillot
 */
class Sendmail extends Mail
{
    /**
     * Get SwiftMailer transport
     *
     * @access protected
     * @return \Swift_Transport
     */
    protected function getTransport()
    {
        return Swift_SendmailTransport::newInstance(MAIL_SENDMAIL_COMMAND);
    }
}
