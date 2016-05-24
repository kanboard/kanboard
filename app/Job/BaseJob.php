<?php

namespace Kanboard\Job;

use Kanboard\Core\Base;

/**
 * Class BaseJob
 *
 * @package Kanboard\Job
 * @author  Frederic Guillot
 */
abstract class BaseJob extends Base
{
    /**
     * Job parameters
     *
     * @access protected
     * @var array
     */
    protected $jobParams = array();

    /**
     * Get job parameters
     *
     * @access public
     * @return array
     */
    public function getJobParams()
    {
        return $this->jobParams;
    }
}
