<?php

namespace Kanboard\Core\Queue;

use Kanboard\Core\Base;
use Kanboard\Job\BaseJob;
use SimpleQueue\Job;

/**
 * Class JobHandler
 *
 * @package Kanboard\Core\Queue
 * @author  Frederic Guillot
 */
class JobHandler extends Base
{
    /**
     * Serialize a job
     *
     * @access public
     * @param  BaseJob $job
     * @return Job
     */
    public function serializeJob(BaseJob $job)
    {
        return new Job(array(
            'class' => get_class($job),
            'params' => $job->getJobParams(),
            'user_id' => $this->userSession->getId(),
        ));
    }

    /**
     * Execute a job
     *
     * @access public
     * @param Job $job
     */
    public function executeJob(Job $job)
    {
        $payload = $job->getBody();
        $className = $payload['class'];
        $this->memoryCache->flush();
        $this->prepareJobSession($payload['user_id']);

        if (DEBUG) {
            $this->logger->debug(__METHOD__.' Received job => '.$className.' ('.getmypid().')');
        }

        $worker = new $className($this->container);
        call_user_func_array(array($worker, 'execute'), $payload['params']);
    }

    /**
     * Create the session for the job
     *
     * @access protected
     * @param integer $user_id
     */
    protected function prepareJobSession($user_id)
    {
        $session = array();
        $this->sessionStorage->setStorage($session);

        if ($user_id > 0) {
            $user = $this->userModel->getById($user_id);
            $this->userSession->initialize($user);
        }
    }
}
