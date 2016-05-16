<?php

namespace Kanboard\Core\Controller;

use Kanboard\Core\Base;

/**
 * Class BaseMiddleware
 *
 * @package Kanboard\Core\Controller
 * @author  Frederic Guillot
 */
abstract class BaseMiddleware extends Base
{
    /**
     * @var BaseMiddleware
     */
    protected $nextMiddleware = null;

    /**
     * Execute middleware
     */
    abstract public function execute();

    /**
     * Set next middleware
     *
     * @param  BaseMiddleware $nextMiddleware
     * @return BaseMiddleware
     */
    public function setNextMiddleware(BaseMiddleware $nextMiddleware)
    {
        $this->nextMiddleware = $nextMiddleware;
        return $this;
    }

    /**
     * @return BaseMiddleware
     */
    public function getNextMiddleware()
    {
        return $this->nextMiddleware;
    }

    /**
     * Move to next middleware
     */
    public function next()
    {
        if ($this->nextMiddleware !== null) {
            if (DEBUG) {
                $this->logger->debug(__METHOD__.' => ' . get_class($this->nextMiddleware));
            }

            $this->nextMiddleware->execute();
        }
    }
}
