<?php

namespace ServiceProvider;

use Core\Plugin\Loader;
use Core\ObjectStorage\FileStorage;
use Core\Paginator;
use Core\OAuth2;
use Core\Tool;
use Model\Config;
use Model\Project;
use Model\Webhook;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use League\HTMLToMarkdown\HtmlConverter;

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
            'Currency',
            'CustomFilter',
            'DateParser',
            'File',
            'LastLogin',
            'Link',
            'Notification',
            'NotificationType',
            'NotificationFilter',
            'OverdueNotification',
            'WebNotification',
            'EmailNotification',
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
            'Transition',
            'User',
            'UserSession',
            'Webhook',
        ),
        'Formatter' => array(
            'TaskFilterGanttFormatter',
            'TaskFilterAutoCompleteFormatter',
            'TaskFilterCalendarFormatter',
            'TaskFilterICalendarFormatter',
            'ProjectGanttFormatter',
        ),
        'Core' => array(
            'EmailClient',
            'Helper',
            'HttpClient',
            'Lexer',
            'Request',
            'Router',
            'Session',
            'Template',
        ),
        'Core\Cache' => array(
            'MemoryCache',
        ),
        'Core\Plugin' => array(
            'Hook',
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
        Tool::buildDIC($container, $this->classes);

        $container['paginator'] = $container->factory(function ($c) {
            return new Paginator($c);
        });

        $container['oauth'] = $container->factory(function ($c) {
            return new OAuth2($c);
        });

        $container['htmlConverter'] = function() {
            return new HtmlConverter(array('strip_tags' => true));
        };

        $container['objectStorage'] = function() {
            return new FileStorage(FILES_DIR);
        };

        $container['pluginLoader'] = new Loader($container);

        $container['cspRules'] = array('style-src' => "'self' 'unsafe-inline'", 'img-src' => '* data:');
    }
}
