<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Core\Tool;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class FormatterProvider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class FormatterProvider implements ServiceProviderInterface
{
    protected $formatters = array(
        'Formatter' => array(
            'BoardColumnFormatter',
            'BoardFormatter',
            'BoardSwimlaneFormatter',
            'BoardTaskFormatter',
            'GroupAutoCompleteFormatter',
            'ProjectActivityEventFormatter',
            'ProjectApiFormatter',
            'ProjectsApiFormatter',
            'SubtaskListFormatter',
            'SubtaskTimeTrackingCalendarFormatter',
            'TaskApiFormatter',
            'TasksApiFormatter',
            'TaskAutoCompleteFormatter',
            'TaskICalFormatter',
            'TaskListFormatter',
            'TaskListSubtaskFormatter',
            'TaskListSubtaskAssigneeFormatter',
            'TaskSuggestMenuFormatter',
            'UserAutoCompleteFormatter',
            'UserMentionFormatter',
        )
    );

    /**
     * Registers services on the given container.
     *
     * @param  Container $container
     * @return Container
     */
    public function register(Container $container)
    {
        Tool::buildFactories($container, $this->formatters);
        return $container;
    }
}
