<?php

namespace SimpleQueue\Adapter;

use DateTime;
use Exception;
use SimpleQueue\Exception\NotSupportedException;
use SimpleQueue\QueueAdapterInterface;
use SimpleQueue\Job;
use SplQueue;

/**
 * Class MemoryAdapter
 *
 * @package SimpleQueue\Adapter
 */
class MemoryQueueAdapter implements QueueAdapterInterface
{
    /**
     * @var SplQueue
     */
    protected $queue;

    /**
     * MemoryAdapter constructor.
     */
    public function __construct()
    {
        $this->queue = new SplQueue();
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
        $this->queue->enqueue($job->serialize());
        return $this;
    }

    /**
     * Schedule a job in the future
     *
     * @access public
     * @param  Job      $job
     * @param  DateTime $dateTime
     * @return bool
     * @throws NotSupportedException
     */
    public function schedule(Job $job, DateTime $dateTime)
    {
        throw new NotSupportedException('Job delay is not supported by MemoryQueue');
    }

    /**
     * Wait and get job from a queue
     *
     * @access public
     * @return Job|null
     */
    public function pull()
    {
        try {
            $job = new Job();
            $payload = $this->queue->dequeue();
            return $job->unserialize($payload);
        } catch (Exception $e) {
            return null;
        }
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
        $this->queue->enqueue($job->serialize());
        return $this;
    }
}
