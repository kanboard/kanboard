<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use League\HTMLToMarkdown\HtmlConverter;
use Kanboard\Core\Mail\Client as EmailClient;
use Kanboard\Core\ObjectStorage\FileStorage;
use Kanboard\Core\Paginator;
use Kanboard\Core\Http\OAuth2;
use Kanboard\Core\Tool;
use Kanboard\Core\Http\Client as HttpClient;

class ClassProvider implements ServiceProviderInterface
{
    private $classes = array(
        'Model' => array(
            'Action',
            'Authentication',
            'Board',
            'Category',
            'Color',
            'Comment',
            'Config',
            'Currency',
            'CustomFilter',
            'File',
            'Group',
            'GroupMember',
            'LastLogin',
            'Link',
            'Notification',
            'OverdueNotification',
            'Project',
            'ProjectActivity',
            'ProjectAnalytic',
            'ProjectDuplication',
            'ProjectDailyColumnStats',
            'ProjectDailyStats',
            'ProjectPermission',
            'ProjectNotification',
            'ProjectMetadata',
            'ProjectGroupRole',
            'ProjectUserRole',
            'RememberMeSession',
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
            'TaskMetadata',
            'Transition',
            'User',
            'UserImport',
            'UserLocking',
            'UserNotification',
            'UserNotificationFilter',
            'UserUnreadNotification',
            'UserMetadata',
        ),
        'Formatter' => array(
            'TaskFilterGanttFormatter',
            'TaskFilterAutoCompleteFormatter',
            'TaskFilterCalendarFormatter',
            'TaskFilterICalendarFormatter',
            'ProjectGanttFormatter',
            'UserFilterAutoCompleteFormatter',
            'GroupAutoCompleteFormatter',
        ),
        'Core' => array(
            'DateParser',
            'Helper',
            'Lexer',
            'Template',
        ),
        'Core\Http' => array(
            'Request',
            'Response',
            'RememberMeCookie',
        ),
        'Core\Cache' => array(
            'MemoryCache',
        ),
        'Core\Plugin' => array(
            'Hook',
        ),
        'Core\Security' => array(
            'Token',
            'Role',
        ),
        'Core\User' => array(
            'GroupSync',
            'UserSync',
            'UserSession',
            'UserProfile',
        ),
        'Integration' => array(
            'BitbucketWebhook',
            'GithubWebhook',
            'GitlabWebhook',
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

        $container['httpClient'] = function ($c) {
            return new HttpClient($c);
        };

        $container['htmlConverter'] = function () {
            return new HtmlConverter(array('strip_tags' => true));
        };

        $container['objectStorage'] = function () {
            return new FileStorage(FILES_DIR);
        };

        $container['emailClient'] = function ($container) {
            $mailer = new EmailClient($container);
            $mailer->setTransport('smtp', '\Kanboard\Core\Mail\Transport\Smtp');
            $mailer->setTransport('sendmail', '\Kanboard\Core\Mail\Transport\Sendmail');
            $mailer->setTransport('mail', '\Kanboard\Core\Mail\Transport\Mail');
            return $mailer;
        };

        $container['cspRules'] = array('style-src' => "'self' 'unsafe-inline'", 'img-src' => '* data:');

        return $container;
    }
}
