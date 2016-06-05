<?php

namespace Kanboard\Core\Mail;

use Kanboard\Job\EmailJob;
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
     * @return Client
     */
    public function send($email, $name, $subject, $html)
    {
        if (! empty($email)) {
            $this->queueManager->push(EmailJob::getInstance($this->container)
                ->withParams($email, $name, $subject, $html, $this->getAuthor())
            );
        }

        return $this;
    }

    /**
     * Get email author
     *
     * @access public
     * @return string
     */
    public function getAuthor()
    {
        $author = 'Kanboard';

        if ($this->userSession->isLogged()) {
            $author = e('%s via Kanboard', $this->helper->user->getFullname());
        }

        return $author;
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
