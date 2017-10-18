<?php

namespace Kanboard\Job;

/**
 * Async HTTP Client (fire and forget)
 *
 * @package Kanboard\Job
 * @author  Frederic Guillot
 */
class HttpAsyncJob extends BaseJob
{
    /**
     * Set job parameters
     *
     * @access public
     * @param string $method
     * @param string $url
     * @param string $content
     * @param array  $headers
     * @return $this
     */
    public function withParams($method, $url, $content, array $headers)
    {
        $this->jobParams = array($method, $url, $content, $headers);
        return $this;
    }

    /**
     * Set job parameters
     *
     * @access public
     * @param string $method
     * @param string $url
     * @param string $content
     * @param array  $headers
     */
    public function execute($method, $url, $content, array $headers)
    {
        $this->httpClient->doRequest($method, $url, $content, $headers);
    }
}
