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
            'Budget',
            'Category',
            'Color',
            'Comment',
            'Config',
            'Currency',
            'DateParser',
            'File',
            'HourlyRate',
            'LastLogin',
            'Link',
            'Notification',
            'Project',
            'ProjectActivity',
            'ProjectAnalytic',
            'ProjectDuplication',
            'ProjectDailySummary',
            'ProjectIntegration',
            'ProjectPermission',
            'Subtask',
            'SubtaskExport',
            'SubtaskForecast',
            'SubtaskTimeTracking',
            'Swimlane',
            'Task',
            'TaskCreation',
            'TaskDuplication',
            'TaskExport',
            'TaskFinder',
            'TaskFilter',
            'TaskLink',
            'TaskModification',
            'TaskPermission',
            'TaskPosition',
            'TaskStatus',
            'TaskValidator',
            'Timetable',
            'TimetableDay',
            'TimetableWeek',
            'TimetableOff',
            'TimetableExtra',
            'Transition',
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
            'HttpClient',
        ),
        'Integration' => array(
            'GitlabWebhook',
            'GithubWebhook',
            'BitbucketWebhook',
            'HipchatWebhook',
            'MailgunWebhook',
            'SendgridWebhook',
            'SlackWebhook',
            'PostmarkWebhook',
            'Jabber',
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
