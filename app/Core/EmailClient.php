<?php

namespace Core;

/**
 * Mail client
 *
 * @package  core
 * @author   Frederic Guillot
 */
class EmailClient extends Base
{
    /**
     * Send a HTML email
     *
     * @access public
     * @param  string  $email
     * @param  string  $name
     * @param  string  $subject
     * @param  string  $html
     */
    public function send($email, $name, $subject, $html)
    {
        $this->container['logger']->debug('Sending email to '.$email.' ('.MAIL_TRANSPORT.')');

        $start_time = microtime(true);
        $author = 'Kanboard';

        if (Session::isOpen() && $this->userSession->isLogged()) {
            $author = e('%s via Kanboard', $this->user->getFullname($this->session['user']));
        }

        switch (MAIL_TRANSPORT) {
            case 'sendgrid':
                $this->sendgrid->sendEmail($email, $name, $subject, $html, $author);
                break;
            case 'mailgun':
                $this->mailgun->sendEmail($email, $name, $subject, $html, $author);
                break;
            case 'postmark':
                $this->postmark->sendEmail($email, $name, $subject, $html, $author);
                break;
            default:
                $this->smtp->sendEmail($email, $name, $subject, $html, $author);
        }

        $this->container['logger']->debug('Email sent in '.round(microtime(true) - $start_time, 6).' seconds');
    }
}
