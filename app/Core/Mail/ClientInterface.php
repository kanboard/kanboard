<?php

namespace Kanboard\Core\Mail;

/**
 * Mail Client Interface
 *
 * @package  mail
 * @author   Frederic Guillot
 */
interface ClientInterface
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
    public function sendEmail($email, $name, $subject, $html, $author);
}
