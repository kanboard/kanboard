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
     * @param bool   $raiseForErrors
     * @param bool   $blockPrivateNetworks
     * @return $this
     */
    public function withParams($method, $url, $content, array $headers, $raiseForErrors = false, $blockPrivateNetworks = false)
    {
        $this->jobParams = array($method, $url, $content, $headers, $raiseForErrors, $blockPrivateNetworks);
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
     * @param bool   $raiseForErrors
     * @param bool   $blockPrivateNetworks
     */
    public function execute($method, $url, $content, array $headers, $raiseForErrors = false, $blockPrivateNetworks = false)
    {
        $httpClient = $blockPrivateNetworks ? $this->httpClient->withPrivateNetworksBlocked() : $this->httpClient;
        $httpClient->doRequest($method, $url, $content, $headers, $raiseForErrors);
    }
}
