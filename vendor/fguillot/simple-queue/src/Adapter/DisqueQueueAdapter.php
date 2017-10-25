<?php

namespace SimpleQueue\Adapter;

use DateTime;
use Disque\Client as DisqueClient;
use Disque\Queue\Job as DisqueJob;
use SimpleQueue\Job;
use SimpleQueue\QueueAdapterInterface;

/**
 * Class DisqueQueueAdapter
 *
 * @package SimpleQueue\Adapter
 */
class DisqueQueueAdapter implements QueueAdapterInterface
{
    /**
     * @var DisqueClient
     */
    protected $disque;

    /**
     * @var string
     */
    protected $queueName;

    /**
     * DisqueQueueAdapter constructor.
     *
     * @param DisqueClient $disque
     * @param string       $queueName
     */
    public function __construct(DisqueClient $disque, $queueName)
    {
        $this->disque = $disque;
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
        $this->disque->queue($this->queueName)->push(new DisqueJob($job->getBody()));
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
        $this->disque->queue($this->queueName)->schedule(new DisqueJob($job->serialize()), $dateTime);
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
        $disqueJob = $this->disque->queue($this->queueName)->pull();

        if ($disqueJob === null) {
            return null;
        }

        return new Job($disqueJob->getBody(), $disqueJob->getId());
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
        $this->disque->queue($this->queueName)->processed(new DisqueJob($job->getBody(), $job->getId()));
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
        $this->disque->queue($this->queueName)->failed(new DisqueJob($job->getBody(), $job->getId()));
        return $this;
    }
}
