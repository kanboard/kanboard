<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Swift_SmtpTransport;
use Swift_SendmailTransport;
use Swift_MailTransport;

class Mailer implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['mailer'] = $this->getInstance();
    }

    public function getInstance()
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
