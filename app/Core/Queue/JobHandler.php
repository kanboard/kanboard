<?php

namespace Kanboard\Core\Queue;

use Exception;
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

        try {
            $className = $payload['class'];
            $this->prepareJobSession($payload['user_id']);
            $this->prepareJobEnvironment();

            if (DEBUG) {
                $this->logger->debug(__METHOD__.' Received job => '.$className.' ('.getmypid().')');
                $this->logger->debug(__METHOD__.' => '.json_encode($payload));
            }

            $worker = new $className($this->container);
            call_user_func_array(array($worker, 'execute'), $payload['params']);
        } catch (Exception $e) {
            $this->logger->error(__METHOD__.': Error during job execution: '.$e->getMessage());
            $this->logger->error(__METHOD__ .' => '.json_encode($payload));
        }
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

    /**
     * Flush in-memory caching and specific events
     *
     * @access protected
     */
    protected function prepareJobEnvironment()
    {
        $this->memoryCache->flush();
        $this->actionManager->removeEvents();
        $this->dispatcher->dispatch('app.bootstrap');
    }
}
