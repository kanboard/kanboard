<?php

namespace SimpleQueue;

/**
 * Class Job
 *
 * @package SimpleQueue
 */
class Job
{
    protected $id;
    protected $body;

    /**
     * Job constructor.
     *
     * @param null $body
     * @param null $id
     */
    public function __construct($body = null, $id = null)
    {
        $this->body = $body;
        $this->id = $id;
    }

    /**
     * Unserialize a payload
     *
     * @param  string $payload
     * @return $this
     */
    public function unserialize($payload)
    {
        $this->body = json_decode($payload, true);
        return $this;
    }

    /**
     * Serialize the body
     *
     * @return string
     */
    public function serialize()
    {
        return json_encode($this->body);
    }

    /**
     * Set body
     *
     * @param mixed $body
     * @return Job
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get body
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set job ID
     *
     * @param mixed $jobId
     * @return Job
     */
    public function setId($jobId)
    {
        $this->id = $jobId;
        return $this;
    }

    /**
     * Get job ID
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Execute job
     */
    public function execute()
    {
    }
}
