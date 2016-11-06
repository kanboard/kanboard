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
     * @return ExternalTaskInterface
     */
    public function retrieve($uri);

    /**
     * Get task import template name
     *
     * @return string
     */
    public function getImportFormTemplate();

    /**
     * Get creation form template
     *
     * @return string
     */
    public function getCreationFormTemplate();

    /**
     * Build external task URI based on import form values
     *
     * @param  array $values
     * @return string
     */
    public function buildTaskUri(array $values);
}
