<?php

namespace Kanboard\Core\Mail;

use Pimple\Container;
use Kanboard\Core\Base;

/**
 * Mail Client
 *
 * @package  mail
 * @author   Frederic Guillot
 */
class Client extends Base
{
    /**
     * Mail transport instances
     *
     * @access private
     * @var \Pimple\Container
     */
    private $transports;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->transports = new Container;
    }

    /**
     * Send a HTML email
     *
     * @access public
     * @param  string  $email
     * @param  string  $name
     * @param  string  $subject
     * @param  string  $html
     * @return EmailClient
     */
    public function send($email, $name, $subject, $html)
    {
        $this->container['logger']->debug('Sending email to '.$email.' ('.MAIL_TRANSPORT.')');

        $start_time = microtime(true);
        $author = 'Kanboard';

        if ($this->userSession->isLogged()) {
            $author = e('%s via Kanboard', $this->user->getFullname($this->session['user']));
        }

        $this->getTransport(MAIL_TRANSPORT)->sendEmail($email, $name, $subject, $html, $author);

        if (DEBUG) {
            $this->logger->debug('Email sent in '.round(microtime(true) - $start_time, 6).' seconds');
        }

        return $this;
    }

    /**
     * Get mail transport instance
     *
     * @access public
     * @param  string  $transport
     * @return EmailClientInterface
     */
    public function getTransport($transport)
    {
        return $this->transports[$transport];
    }

    /**
     * Add a new mail transport
     *
     * @access public
     * @param  string  $transport
     * @param  string  $class
     * @return EmailClient
     */
    public function setTransport($transport, $class)
    {
        $container = $this->container;

        $this->transports[$transport] = function () use ($class, $container) {
            return new $class($container);
        };

        return $this;
    }
}
