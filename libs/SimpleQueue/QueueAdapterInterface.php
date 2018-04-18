<?php

namespace SimpleQueue;

use DateTime;

/**
 * Interface AdapterInterface
 *
 * @package SimpleQueue\Adapter
 */
interface QueueAdapterInterface
{
    /**
     * Send a job
     *
     * @access public
     * @param  Job $job
     * @return $this
     */
    public function push(Job $job);

    /**
     * Schedule a job in the future
     *
     * @access public
     * @param  Job      $job
     * @param  DateTime $dateTime
     * @return $this
     */
    public function schedule(Job $job, DateTime $dateTime);

    /**
     * Wait and get job from a queue
     *
     * @access public
     * @return Job|null
     */
    public function pull();

    /**
     * Acknowledge a job
     *
     * @access public
     * @param  Job $job
     * @return $this
     */
    public function completed(Job $job);

    /**
     * Mark a job as failed
     *
     * @access public
     * @param  Job $job
     * @return $this
     */
    public function failed(Job $job);
}
