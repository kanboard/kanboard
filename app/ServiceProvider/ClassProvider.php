<?php

namespace ServiceProvider;

use Model\Config;
use Model\Project;
use Model\Webhook;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ClassProvider implements ServiceProviderInterface
{
    private $classes = array(
        'Model' => array(
            'Acl',
            'Action',
            'Authentication',
            'Board',
            'Category',
            'Color',
            'Comment',
            'Config',
            'DateParser',
            'File',
            'GithubWebhook',
            'LastLogin',
            'Notification',
            'Project',
            'ProjectActivity',
            'ProjectAnalytics',
            'ProjectDailySummary',
            'ProjectPaginator',
            'ProjectPermission',
            'SubTask',
            'SubtaskPaginator',
            'Swimlane',
            'Task',
            'TaskCreation',
            'TaskDuplication',
            'TaskExport',
            'TaskFinder',
            'TaskModification',
            'TaskPaginator',
            'TaskPermission',
            'TaskPosition',
            'TaskStatus',
            'TaskValidator',
            'TimeTracking',
            'User',
            'Webhook',
        ),
        'Core' => array(
            'Template',
            'Session',
        ),
    );

    public function register(Container $container)
    {
        foreach ($this->classes as $namespace => $classes) {

            foreach ($classes as $name) {

                $class = '\\'.$namespace.'\\'.$name;

                $container[lcfirst($name)] = function ($c) use ($class) {
                    return new $class($c);
                };
            }
        }
    }
}
