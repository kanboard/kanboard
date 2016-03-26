<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\Mail\Client as EmailClient;
use Kanboard\Core\ObjectStorage\FileStorage;
use Kanboard\Core\Paginator;
use Kanboard\Core\Http\OAuth2;
use Kanboard\Core\Tool;
use Kanboard\Core\Http\Client as HttpClient;

class ClassProvider implements ServiceProviderInterface
{
    private $classes = array(
        'Analytic' => array(
            'TaskDistributionAnalytic',
            'UserDistributionAnalytic',
            'EstimatedTimeComparisonAnalytic',
            'AverageLeadCycleTimeAnalytic',
            'AverageTimeSpentColumnAnalytic',
        ),
        'Model' => array(
            'Action',
            'ActionParameter',
            'AvatarFile',
            'Board',
            'Category',
            'Color',
            'Column',
            'Comment',
            'Config',
            'Currency',
            'CustomFilter',
            'Group',
            'GroupMember',
            'LastLogin',
            'Link',
            'Notification',
            'PasswordReset',
            'Project',
            'ProjectFile',
            'ProjectActivity',
            'ProjectDuplication',
            'ProjectDailyColumnStats',
            'ProjectDailyStats',
            'ProjectPermission',
            'ProjectNotification',
            'ProjectMetadata',
            'ProjectGroupRole',
            'ProjectGroupRoleFilter',
            'ProjectUserRole',
            'ProjectUserRoleFilter',
            'RememberMeSession',
            'Subtask',
            'SubtaskTimeTracking',
            'Swimlane',
            'Task',
            'TaskAnalytic',
            'TaskCreation',
            'TaskDuplication',
            'TaskExternalLink',
            'TaskFinder',
            'TaskFile',
            'TaskFilter',
            'TaskLink',
            'TaskModification',
            'TaskPermission',
            'TaskPosition',
            'TaskStatus',
            'TaskMetadata',
            'Transition',
            'User',
            'UserLocking',
            'UserMention',
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
        'Validator' => array(
            'ActionValidator',
            'AuthValidator',
            'CategoryValidator',
            'ColumnValidator',
            'CommentValidator',
            'CurrencyValidator',
            'CustomFilterValidator',
            'ExternalLinkValidator',
            'GroupValidator',
            'LinkValidator',
            'PasswordResetValidator',
            'ProjectValidator',
            'SubtaskValidator',
            'SwimlaneValidator',
            'TaskValidator',
            'TaskLinkValidator',
            'UserValidator',
        ),
        'Import' => array(
            'TaskImport',
            'UserImport',
        ),
        'Export' => array(
            'SubtaskExport',
            'TaskExport',
            'TransitionExport',
        ),
        'Core' => array(
            'DateParser',
            'Lexer',
        ),
        'Core\Event' => array(
            'EventManager',
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

        $container['cspRules'] = array(
            'default-src' => "'self'",
            'style-src' => "'self' 'unsafe-inline'",
            'img-src' => '* data:',
        );

        return $container;
    }
}
