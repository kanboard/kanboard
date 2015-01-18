<?php

namespace ServiceProvider;

use Core\Paginator;
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
            'LastLogin',
            'Notification',
            'Project',
            'ProjectActivity',
            'ProjectAnalytic',
            'ProjectDailySummary',
            'ProjectPermission',
            'SubTask',
            'SubtaskExport',
            'Swimlane',
            'Task',
            'TaskCreation',
            'TaskDuplication',
            'TaskExport',
            'TaskFinder',
            'TaskFilter',
            'TaskModification',
            'TaskPermission',
            'TaskPosition',
            'TaskStatus',
            'TaskValidator',
            'TimeTracking',
            'User',
            'UserSession',
            'Webhook',
        ),
        'Core' => array(
            'Helper',
            'Template',
            'Session',
            'MemoryCache',
            'FileCache',
            'Request',
        ),
        'Integration' => array(
            'GitlabWebhook',
            'GithubWebhook',
        )
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

        $container['paginator'] = $container->factory(function ($c) {
            return new Paginator($c);
        });
    }
}
