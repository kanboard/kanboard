<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Class MailHelper
 *
 * @package Kanboard\Helper
 * @author  Frederic Guillot
 */
class MailHelper extends Base
{
    /**
     * Filter mail subject
     *
     * @access public
     * @param  string $subject
     * @return string
     */
    public function filterSubject($subject)
    {
        $subject = str_ireplace('RE: ', '', $subject);
        $subject = str_ireplace('FW: ', '', $subject);
        $subject = str_ireplace('Fwd: ', '', $subject);

        return $subject;
    }

    /**
     * Get mail sender address
     *
     * @access public
     * @return string
     */
    public function getMailSenderAddress()
    {
        if (MAIL_CONFIGURATION) {
            $email = $this->configModel->get('mail_sender_address');

            if (! empty($email)) {
                return $email;
            }
        }

        return MAIL_FROM;
    }

    /**
     * Get mail transport
     *
     * @access public
     * @return string
     */
    public function getMailTransport()
    {
        if (MAIL_CONFIGURATION) {
            $transport = $this->configModel->get('mail_transport');

            if (! empty($transport)) {
                return $transport;
            }
        }

        return MAIL_TRANSPORT;
    }
}
