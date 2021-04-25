<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\Paginator;
use Kanboard\Core\Http\OAuth2;
use Kanboard\Core\Tool;
use Kanboard\Core\Http\Client as HttpClient;

/**
 * Class ClassProvider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class ClassProvider implements ServiceProviderInterface
{
    private $classes = array(
        'Analytic' => array(
            'TaskDistributionAnalytic',
            'UserDistributionAnalytic',
            'EstimatedTimeComparisonAnalytic',
            'AverageLeadCycleTimeAnalytic',
            'AverageTimeSpentColumnAnalytic',
            'EstimatedActualColumnAnalytic',
        ),
        'Model' => array(
            'ActionModel',
            'ActionParameterModel',
            'AvatarFileModel',
            'BoardModel',
            'CategoryModel',
            'ColorModel',
            'ColumnModel',
            'ColumnRestrictionModel',
            'ColumnMoveRestrictionModel',
            'CommentModel',
            'ConfigModel',
            'CurrencyModel',
            'CustomFilterModel',
            'GroupModel',
            'GroupMemberModel',
            'InviteModel',
            'LanguageModel',
            'LastLoginModel',
            'LinkModel',
            'NotificationModel',
            'PasswordResetModel',
            'PredefinedTaskDescriptionModel',
            'ProjectModel',
            'ProjectFileModel',
            'ProjectActivityModel',
            'ProjectDuplicationModel',
            'ProjectDailyColumnStatsModel',
            'ProjectDailyStatsModel',
            'ProjectPermissionModel',
            'ProjectNotificationModel',
            'ProjectMetadataModel',
            'ProjectGroupRoleModel',
            'ProjectRoleModel',
            'ProjectRoleRestrictionModel',
            'ProjectTaskDuplicationModel',
            'ProjectTaskPriorityModel',
            'ProjectUserRoleModel',
            'RememberMeSessionModel',
            'SubtaskModel',
            'SubtaskPositionModel',
            'SubtaskStatusModel',
            'SubtaskTaskConversionModel',
            'SubtaskTimeTrackingModel',
            'SwimlaneModel',
            'TagDuplicationModel',
            'TagModel',
            'TaskModel',
            'TaskAnalyticModel',
            'TaskCreationModel',
            'TaskDuplicationModel',
            'TaskProjectDuplicationModel',
            'TaskProjectMoveModel',
            'TaskRecurrenceModel',
            'TaskExternalLinkModel',
            'TaskFinderModel',
            'TaskFileModel',
            'TaskLinkModel',
            'TaskModificationModel',
            'TaskPositionModel',
            'TaskReorderModel',
            'TaskStatusModel',
            'TaskTagModel',
            'TaskMetadataModel',
            'TimezoneModel',
            'TransitionModel',
            'UserModel',
            'UserLockingModel',
            'UserNotificationModel',
            'UserNotificationFilterModel',
            'UserUnreadNotificationModel',
            'UserMetadataModel',
        ),
        'Validator' => array(
            'ActionValidator',
            'AuthValidator',
            'CategoryValidator',
            'ColumnMoveRestrictionValidator',
            'ColumnRestrictionValidator',
            'ColumnValidator',
            'CommentValidator',
            'CurrencyValidator',
            'CustomFilterValidator',
            'ExternalLinkValidator',
            'GroupValidator',
            'LinkValidator',
            'PasswordResetValidator',
            'ProjectValidator',
            'ProjectRoleValidator',
            'SubtaskValidator',
            'SwimlaneValidator',
            'TagValidator',
            'TaskLinkValidator',
            'TaskValidator',
            'UserValidator',
            'PredefinedTaskDescriptionValidator',
        ),
        'Import' => array(
            'UserImport',
        ),
        'Export' => array(
            'SubtaskExport',
            'TaskExport',
            'TransitionExport',
        ),
        'Pagination' => array(
            'DashboardPagination',
            'ProjectPagination',
            'SubtaskPagination',
            'TaskPagination',
            'UserPagination',
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

        $container['cspRules'] = array(
            'default-src' => "'self'",
            'style-src' => "'self' 'unsafe-inline'",
            'img-src' => '* data:',
        );

        return $container;
    }
}
