<?php

namespace Kanboard\Core\Mail;

/**
 * Mail Client Interface
 *
 * @package  Kanboard\Core\Mail
 * @author   Frederic Guillot
 */
interface ClientInterface
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
    public function sendEmail($recipientEmail, $recipientName, $subject, $html, $authorName, $authorEmail = '');
}
