<?php

namespace Kanboard\Core\Mail;

use Kanboard\Job\EmailJob;
use Pimple\Container;
use Kanboard\Core\Base;

/**
 * Mail Client
 *
 * @package  Kanboard\Core\Mail
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
     * @param  string  $recipientEmail
     * @param  string  $recipientName
     * @param  string  $subject
     * @param  string  $html
     * @return Client
     */
    public function send($recipientEmail, $recipientName, $subject, $html)
    {
        if (! empty($recipientEmail)) {
            $this->queueManager->push(EmailJob::getInstance($this->container)->withParams(
                $recipientEmail,
                $recipientName,
                $subject,
                $html,
                $this->getAuthorName(),
                $this->getAuthorEmail()
            ));
        }

        return $this;
    }

    /**
     * Get author name
     *
     * @access public
     * @return string
     */
    public function getAuthorName()
    {
        $author = 'Kanboard';

        if ($this->userSession->isLogged()) {
            $author = e('%s via Kanboard', $this->helper->user->getFullname());
        }

        return $author;
    }

    /**
     * Get author email
     *
     * @access public
     * @return string
     */
    public function getAuthorEmail()
    {
        if ($this->userSession->isLogged()) {
            $userData = $this->userSession->getAll();
            return ! empty($userData['email']) ? $userData['email'] : '';
        }

        return '';
    }

    /**
     * Get mail transport instance
     *
     * @access public
     * @param  string  $transport
     * @return ClientInterface
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
     * @return Client
     */
    public function setTransport($transport, $class)
    {
        $container = $this->container;

        $this->transports[$transport] = function () use ($class, $container) {
            return new $class($container);
        };

        return $this;
    }

    /**
     * Return the list of registered transports
     *
     * @access public
     * @return array
     */
    public function getAvailableTransports()
    {
        $availableTransports = $this->transports->keys();
        return array_combine($availableTransports, $availableTransports);
    }
}
