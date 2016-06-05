<?php

namespace Kanboard\Job;

/**
 * Class EmailJob
 *
 * @package Kanboard\Job
 * @author  Frederic Guillot
 */
class EmailJob extends BaseJob
{
    /**
     * Set job parameters
     *
     * @access public
     * @param  string $email
     * @param  string $name
     * @param  string $subject
     * @param  string $html
     * @param  string $author
     * @return $this
     */
    public function withParams($email, $name, $subject, $html, $author)
    {
        $this->jobParams = array($email, $name, $subject, $html, $author);
        return $this;
    }

    /**
     * Execute job
     *
     * @access public
     * @param  string $email
     * @param  string $name
     * @param  string $subject
     * @param  string $html
     * @param  string $author
     */
    public function execute($email, $name, $subject, $html, $author)
    {
        $transport = $this->helper->mail->getMailTransport();
        $this->logger->debug(__METHOD__.' Sending email to: '.$email.' using transport: '.$transport);
        $startTime = microtime(true);

        $this->emailClient
            ->getTransport($transport)
            ->sendEmail($email, $name, $subject, $html, $author)
        ;

        if (DEBUG) {
            $this->logger->debug('Email sent in '.round(microtime(true) - $startTime, 6).' seconds');
        }
    }
}
