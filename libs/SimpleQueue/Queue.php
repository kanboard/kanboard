<?php

namespace SimpleQueue;

use DateTime;

/**
 * Class Queue
 *
 * @package SimpleQueue
 */
class Queue implements QueueAdapterInterface
{
    /**
     * @var QueueAdapterInterface
     */
    protected $queueAdapter;

    /**
     * Queue constructor.
     *
     * @param QueueAdapterInterface $queueAdapter
     */
    public function __construct(QueueAdapterInterface $queueAdapter)
    {
        $this->queueAdapter = $queueAdapter;
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
        $this->queueAdapter->push($job);
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
        $this->queueAdapter->schedule($job, $dateTime);
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
        return $this->queueAdapter->pull();
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
        $this->queueAdapter->completed($job);
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
        $this->queueAdapter->failed($job);
        return $this;
    }
}
