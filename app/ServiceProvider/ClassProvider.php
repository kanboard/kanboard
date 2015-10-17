<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Core\Plugin\Loader;
use Kanboard\Core\Mail\Client as EmailClient;
use Kanboard\Core\ObjectStorage\FileStorage;
use Kanboard\Core\Paginator;
use Kanboard\Core\OAuth2;
use Kanboard\Core\Tool;
use Kanboard\Model\Config;
use Kanboard\Model\Project;
use Kanboard\Model\Webhook;
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
            'TaskImport',
            'Transition',
            'User',
            'UserImport',
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
            'SlackWebhook',
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

        $container['emailClient'] = function($container) {
            $mailer = new EmailClient($container);
            $mailer->setTransport('smtp', '\Kanboard\Core\Mail\Transport\Smtp');
            $mailer->setTransport('sendmail', '\Kanboard\Core\Mail\Transport\Sendmail');
            $mailer->setTransport('mail', '\Kanboard\Core\Mail\Transport\Mail');
            return $mailer;
        };

        $container['pluginLoader'] = new Loader($container);

        $container['cspRules'] = array('style-src' => "'self' 'unsafe-inline'", 'img-src' => '* data:');
    }
}
