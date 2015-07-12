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
            'ProjectDailyColumnStats',
            'ProjectDailyStats',
            'ProjectIntegration',
            'ProjectPermission',
            'Subtask',
            'SubtaskExport',
            'SubtaskForecast',
            'SubtaskTimeTracking',
            'Swimlane',
            'Task',
            'TaskAnalytic',
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
            'TimetableExtra',
            'TimetableWeek',
            'TimetableOff',
            'Transition',
            'User',
            'UserSession',
            'Webhook',
        ),
        'Core' => array(
            'EmailClient',
            'Helper',
            'HttpClient',
            'Lexer',
            'MemoryCache',
            'Request',
            'Router',
            'Session',
            'Template',
        ),
        'Integration' => array(
            'BitbucketWebhook',
            'GithubWebhook',
            'GitlabWebhook',
            'HipchatWebhook',
            'Jabber',
            'Mailgun',
            'Postmark',
            'Sendgrid',
            'SlackWebhook',
            'Smtp',
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
