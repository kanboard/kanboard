<?php

namespace Kanboard\Core\ExternalTask;

/**
 * Interface ExternalTaskProviderInterface
 *
 * @package Kanboard\Core\ExternalTask
 * @author  Frederic Guillot
 */
interface ExternalTaskProviderInterface
{
    /**
     * Get templates
     *
     * @return string
     */
    public function getCreationFormTemplate();
    public function getModificationFormTemplate();
    public function getTaskViewTemplate();

    /**
     * Get provider name (visible in the user interface)
     *
     * @access public
     * @return string
     */
    public function getName();

    /**
     * Retrieve task from external system or cache
     *
     * @access public
     * @throws \Kanboard\Core\ExternalTask\AccessForbiddenException
     * @throws \Kanboard\Core\ExternalTask\NotFoundException
     * @param  string $uri
     * @return array       Dict that will populate the form
     */
    public function retrieve($uri);

    /**
     * Save the task to the external system and/or update the cache
     *
     * @access public
     * @param  string  $uri
     * @param  array   $data
     * @return bool
     */
    public function persist($uri, array $data);
}
