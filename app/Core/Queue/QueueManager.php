<?php

namespace Kanboard\Core\Queue;

use Kanboard\Core\Base;
use Kanboard\Job\BaseJob;
use LogicException;
use SimpleQueue\Queue;

/**
 * Class QueueManager
 *
 * @package Kanboard\Core\Queue
 * @author  Frederic Guillot
 */
class QueueManager extends Base
{
    /**
     * @var Queue
     */
    protected $queue = null;

    /**
     * Set queue driver
     *
     * @access public
     * @param  Queue $queue
     * @return $this
     */
    public function setQueue(Queue $queue)
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * Send a new job to the queue
     *
     * @access public
     * @param  BaseJob $job
     * @return $this
     */
    public function push(BaseJob $job)
    {
        if ($this->queue !== null) {
            $this->queue->push(JobHandler::getInstance($this->container)->serializeJob($job));
        } else {
            call_user_func_array(array($job, 'execute'), $job->getJobParams());
        }

        return $this;
    }

    /**
     * Wait for new jobs
     *
     * @access public
     * @throws LogicException
     */
    public function listen()
    {
        if ($this->queue === null) {
            throw new LogicException('No Queue Driver defined!');
        }

        while ($job = $this->queue->pull()) {
            JobHandler::getInstance($this->container)->executeJob($job);
            $this->queue->completed($job);
        }
    }
}
