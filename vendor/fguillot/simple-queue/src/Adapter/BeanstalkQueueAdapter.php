<?php

namespace SimpleQueue\Adapter;

use DateTime;
use Pheanstalk\Job as BeanstalkJob;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use SimpleQueue\Job;
use SimpleQueue\QueueAdapterInterface;

/**
 * Class BeanstalkQueueAdapter
 *
 * @package SimpleQueue\Adapter
 */
class BeanstalkQueueAdapter implements QueueAdapterInterface
{
    /**
     * @var PheanstalkInterface
     */
    protected $beanstalk;

    /**
     * @var string
     */
    protected $queueName = '';

    /**
     * BeanstalkQueueAdapter constructor.
     *
     * @param PheanstalkInterface $beanstalk
     * @param string              $queueName
     */
    public function __construct(PheanstalkInterface $beanstalk, $queueName)
    {
        $this->beanstalk = $beanstalk;
        $this->queueName = $queueName;
    }

    /**
     * Send a job
     *
     * @access public
     * @param  Job $job
     * @return $this
     */
    public function push(Job $job)
    {
        $this->beanstalk->putInTube($this->queueName, $job->serialize());
        return $this;
    }

    /**
     * Schedule a job in the future
     *
     * @access public
     * @param  Job      $job
     * @param  DateTime $dateTime
     * @return $this
     */
    public function schedule(Job $job, DateTime $dateTime)
    {
        $now = new DateTime();
        $when = clone($dateTime);
        $delay = $when->getTimestamp() - $now->getTimestamp();
        
        $this->beanstalk->putInTube($this->queueName, $job->serialize(), Pheanstalk::DEFAULT_PRIORITY, $delay);
        return $this;
    }

    /**
     * Wait and get job from a queue
     *
     * @access public
     * @return Job|null
     */
    public function pull()
    {
        $beanstalkJob = $this->beanstalk->reserveFromTube($this->queueName);

        if ($beanstalkJob === false) {
            return null;
        }

        $job = new Job();
        $job->setId($beanstalkJob->getId());
        $job->unserialize($beanstalkJob->getData());

        return $job;
    }

    /**
     * Acknowledge a job
     *
     * @access public
     * @param  Job $job
     * @return $this
     */
    public function completed(Job $job)
    {
        $beanstalkJob = new BeanstalkJob($job->getId(), $job->serialize());
        $this->beanstalk->delete($beanstalkJob);
        return $this;
    }

    /**
     * Mark a job as failed
     *
     * @access public
     * @param  Job $job
     * @return $this
     */
    public function failed(Job $job)
    {
        $beanstalkJob = new BeanstalkJob($job->getId(), $job->serialize());
        $this->beanstalk->bury($beanstalkJob);
        return $this;
    }
}
