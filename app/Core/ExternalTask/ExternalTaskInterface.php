<?php

namespace Kanboard\Core\ExternalTask;

/**
 * Interface ExternalTaskInterface
 *
 * @package Kanboard\Core\ExternalTask
 * @author  Frederic Guillot
 */
interface ExternalTaskInterface
{
    /**
     * Return Uniform Resource Identifier for the task
     *
     * @return string
     */
    public function getUri();

    /**
     * Return a dict to populate the task form
     *
     * @return array
     */
    public function getFormValues();
}
