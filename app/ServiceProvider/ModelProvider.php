<?php

namespace ServiceProvider;

use Model\Config;
use Model\Project;
use Model\Webhook;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ModelProvider implements ServiceProviderInterface
{
    private $models = array(
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
    );

    public function register(Container $container)
    {
        foreach ($this->models as $model) {

            $class = '\Model\\'.$model;

            $container[lcfirst($model)] = function ($c) use ($class) {
                return new $class($c);
            };
        }
    }
}
