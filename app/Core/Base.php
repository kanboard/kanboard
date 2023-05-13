<?php

namespace Kanboard\Core;

use Pimple\Container;

/**
 * Base Class
 *
 * @package Kanboard\Core
 * @author  Frederic Guillot
 *
 * @property \Kanboard\Analytic\TaskDistributionAnalytic             $taskDistributionAnalytic
 * @property \Kanboard\Analytic\UserDistributionAnalytic             $userDistributionAnalytic
 * @property \Kanboard\Analytic\EstimatedTimeComparisonAnalytic      $estimatedTimeComparisonAnalytic
 * @property \Kanboard\Analytic\AverageLeadCycleTimeAnalytic         $averageLeadCycleTimeAnalytic
 * @property \Kanboard\Analytic\AverageTimeSpentColumnAnalytic       $averageTimeSpentColumnAnalytic
 * @property \Kanboard\Analytic\EstimatedActualColumnAnalytic        $estimatedActualColumnAnalytic
 * @property \Kanboard\Core\Action\ActionManager                     $actionManager
 * @property \Kanboard\Core\ExternalLink\ExternalLinkManager         $externalLinkManager
 * @property \Kanboard\Core\ExternalTask\ExternalTaskManager         $externalTaskManager
 * @property \Kanboard\Core\Cache\MemoryCache                        $memoryCache
 * @property \Kanboard\Core\Cache\BaseCache                          $cacheDriver
 * @property \Kanboard\Core\Event\EventManager                       $eventManager
 * @property \Kanboard\Core\Group\GroupManager                       $groupManager
 * @property \Kanboard\Core\User\UserManager                         $userManager
 * @property \Kanboard\Core\Http\Client                              $httpClient
 * @property \Kanboard\Core\Http\OAuth2                              $oauth
 * @property \Kanboard\Core\Http\RememberMeCookie                    $rememberMeCookie
 * @property \Kanboard\Core\Http\Request                             $request
 * @property \Kanboard\Core\Http\Response                            $response
 * @property \Kanboard\Core\Http\Router                              $router
 * @property \Kanboard\Core\Http\Route                               $route
 * @property \Kanboard\Core\Queue\QueueManager                       $queueManager
 * @property \Kanboard\Core\Mail\Client                              $emailClient
 * @property \Kanboard\Core\ObjectStorage\ObjectStorageInterface     $objectStorage
 * @property \Kanboard\Core\Plugin\Hook                              $hook
 * @property \Kanboard\Core\Plugin\Loader                            $pluginLoader
 * @property \Kanboard\Core\Security\AuthenticationManager           $authenticationManager
 * @property \Kanboard\Core\Security\AccessMap                       $applicationAccessMap
 * @property \Kanboard\Core\Security\AccessMap                       $projectAccessMap
 * @property \Kanboard\Core\Security\AccessMap                       $apiAccessMap
 * @property \Kanboard\Core\Security\AccessMap                       $apiProjectAccessMap
 * @property \Kanboard\Core\Security\Authorization                   $applicationAuthorization
 * @property \Kanboard\Core\Security\Authorization                   $projectAuthorization
 * @property \Kanboard\Core\Security\Authorization                   $apiAuthorization
 * @property \Kanboard\Core\Security\Authorization                   $apiProjectAuthorization
 * @property \Kanboard\Core\Security\Role                            $role
 * @property \Kanboard\Core\Security\Token                           $token
 * @property \Kanboard\Core\Session\FlashMessage                     $flash
 * @property \Kanboard\Core\Session\SessionManager                   $sessionManager
 * @property \Kanboard\Core\User\Avatar\AvatarManager                $avatarManager
 * @property \Kanboard\Core\User\GroupSync                           $groupSync
 * @property \Kanboard\Core\User\UserProfile                         $userProfile
 * @property \Kanboard\Core\User\UserSync                            $userSync
 * @property \Kanboard\Core\User\UserSession                         $userSession
 * @property \Kanboard\Core\DateParser                               $dateParser
 * @property \Kanboard\Core\Helper                                   $helper
 * @property \Kanboard\Core\Paginator                                $paginator
 * @property \Kanboard\Core\Template                                 $template
 * @property \Kanboard\Decorator\MetadataCacheDecorator              $userMetadataCacheDecorator
 * @property \Kanboard\Decorator\UserCacheDecorator                  $userCacheDecorator
 * @property \Kanboard\Decorator\ColumnRestrictionCacheDecorator     $columnRestrictionCacheDecorator
 * @property \Kanboard\Decorator\ColumnMoveRestrictionCacheDecorator $columnMoveRestrictionCacheDecorator
 * @property \Kanboard\Decorator\ProjectRoleRestrictionCacheDecorator $projectRoleRestrictionCacheDecorator
 * @property \Kanboard\Formatter\BoardColumnFormatter                $boardColumnFormatter
 * @property \Kanboard\Formatter\BoardFormatter                      $boardFormatter
 * @property \Kanboard\Formatter\BoardSwimlaneFormatter              $boardSwimlaneFormatter
 * @property \Kanboard\Formatter\BoardTaskFormatter                  $boardTaskFormatter
 * @property \Kanboard\Formatter\GroupAutoCompleteFormatter          $groupAutoCompleteFormatter
 * @property \Kanboard\Formatter\ProjectActivityEventFormatter       $projectActivityEventFormatter
 * @property \Kanboard\Formatter\ProjectApiFormatter                 $projectApiFormatter
 * @property \Kanboard\Formatter\ProjectsApiFormatter                $projectsApiFormatter
 * @property \Kanboard\Formatter\SubtaskListFormatter                $subtaskListFormatter
 * @property \Kanboard\Formatter\SubtaskTimeTrackingCalendarFormatter $subtaskTimeTrackingCalendarFormatter
 * @property \Kanboard\Formatter\TaskApiFormatter                    $taskApiFormatter
 * @property \Kanboard\Formatter\TasksApiFormatter                   $tasksApiFormatter
 * @property \Kanboard\Formatter\TaskAutoCompleteFormatter           $taskAutoCompleteFormatter
 * @property \Kanboard\Formatter\TaskICalFormatter                   $taskICalFormatter
 * @property \Kanboard\Formatter\TaskListFormatter                   $taskListFormatter
 * @property \Kanboard\Formatter\TaskListSubtaskFormatter            $taskListSubtaskFormatter
 * @property \Kanboard\Formatter\TaskListSubtaskAssigneeFormatter    $taskListSubtaskAssigneeFormatter
 * @property \Kanboard\Formatter\TaskSuggestMenuFormatter            $taskSuggestMenuFormatter
 * @property \Kanboard\Formatter\UserAutoCompleteFormatter           $userAutoCompleteFormatter
 * @property \Kanboard\Formatter\UserMentionFormatter                $userMentionFormatter
 * @property \Kanboard\Model\ActionModel                             $actionModel
 * @property \Kanboard\Model\ActionParameterModel                    $actionParameterModel
 * @property \Kanboard\Model\AvatarFileModel                         $avatarFileModel
 * @property \Kanboard\Model\BoardModel                              $boardModel
 * @property \Kanboard\Model\CategoryModel                           $categoryModel
 * @property \Kanboard\Model\ColorModel                              $colorModel
 * @property \Kanboard\Model\ColumnModel                             $columnModel
 * @property \Kanboard\Model\ColumnRestrictionModel                  $columnRestrictionModel
 * @property \Kanboard\Model\ColumnMoveRestrictionModel              $columnMoveRestrictionModel
 * @property \Kanboard\Model\CommentModel                            $commentModel
 * @property \Kanboard\Model\ConfigModel                             $configModel
 * @property \Kanboard\Model\CurrencyModel                           $currencyModel
 * @property \Kanboard\Model\CustomFilterModel                       $customFilterModel
 * @property \Kanboard\Model\TaskFileModel                           $taskFileModel
 * @property \Kanboard\Model\ProjectFileModel                        $projectFileModel
 * @property \Kanboard\Model\GroupModel                              $groupModel
 * @property \Kanboard\Model\GroupMemberModel                        $groupMemberModel
 * @property \Kanboard\Model\InviteModel                             $inviteModel
 * @property \Kanboard\Model\LanguageModel                           $languageModel
 * @property \Kanboard\Model\LastLoginModel                          $lastLoginModel
 * @property \Kanboard\Model\LinkModel                               $linkModel
 * @property \Kanboard\Model\NotificationModel                       $notificationModel
 * @property \Kanboard\Model\PasswordResetModel                      $passwordResetModel
 * @property \Kanboard\Model\PredefinedTaskDescriptionModel          $predefinedTaskDescriptionModel
 * @property \Kanboard\Model\ProjectModel                            $projectModel
 * @property \Kanboard\Model\ProjectActivityModel                    $projectActivityModel
 * @property \Kanboard\Model\ProjectDuplicationModel                 $projectDuplicationModel
 * @property \Kanboard\Model\ProjectDailyColumnStatsModel            $projectDailyColumnStatsModel
 * @property \Kanboard\Model\ProjectDailyStatsModel                  $projectDailyStatsModel
 * @property \Kanboard\Model\ProjectMetadataModel                    $projectMetadataModel
 * @property \Kanboard\Model\ProjectPermissionModel                  $projectPermissionModel
 * @property \Kanboard\Model\ProjectUserRoleModel                    $projectUserRoleModel
 * @property \Kanboard\Model\ProjectGroupRoleModel                   $projectGroupRoleModel
 * @property \Kanboard\Model\ProjectNotificationModel                $projectNotificationModel
 * @property \Kanboard\Model\ProjectNotificationTypeModel            $projectNotificationTypeModel
 * @property \Kanboard\Model\ProjectRoleModel                        $projectRoleModel
 * @property \Kanboard\Model\ProjectRoleRestrictionModel             $projectRoleRestrictionModel
 * @property \Kanboard\Model\ProjectTaskDuplicationModel             $projectTaskDuplicationModel
 * @property \Kanboard\Model\ProjectTaskPriorityModel                $projectTaskPriorityModel
 * @property \Kanboard\Model\RememberMeSessionModel                  $rememberMeSessionModel
 * @property \Kanboard\Model\SubtaskModel                            $subtaskModel
 * @property \Kanboard\Model\SubtaskPositionModel                    $subtaskPositionModel
 * @property \Kanboard\Model\SubtaskStatusModel                      $subtaskStatusModel
 * @property \Kanboard\Model\SubtaskTaskConversionModel              $subtaskTaskConversionModel
 * @property \Kanboard\Model\SubtaskTimeTrackingModel                $subtaskTimeTrackingModel
 * @property \Kanboard\Model\SwimlaneModel                           $swimlaneModel
 * @property \Kanboard\Model\TagDuplicationModel                     $tagDuplicationModel
 * @property \Kanboard\Model\TagModel                                $tagModel
 * @property \Kanboard\Model\TaskModel                               $taskModel
 * @property \Kanboard\Model\TaskAnalyticModel                       $taskAnalyticModel
 * @property \Kanboard\Model\TaskCreationModel                       $taskCreationModel
 * @property \Kanboard\Model\TaskDuplicationModel                    $taskDuplicationModel
 * @property \Kanboard\Model\TaskProjectDuplicationModel             $taskProjectDuplicationModel
 * @property \Kanboard\Model\TaskProjectMoveModel                    $taskProjectMoveModel
 * @property \Kanboard\Model\TaskRecurrenceModel                     $taskRecurrenceModel
 * @property \Kanboard\Model\TaskExternalLinkModel                   $taskExternalLinkModel
 * @property \Kanboard\Model\TaskFinderModel                         $taskFinderModel
 * @property \Kanboard\Model\TaskLinkModel                           $taskLinkModel
 * @property \Kanboard\Model\TaskModificationModel                   $taskModificationModel
 * @property \Kanboard\Model\TaskPositionModel                       $taskPositionModel
 * @property \Kanboard\Model\TaskReorderModel                        $taskReorderModel
 * @property \Kanboard\Model\TaskStatusModel                         $taskStatusModel
 * @property \Kanboard\Model\TaskTagModel                            $taskTagModel
 * @property \Kanboard\Model\TaskMetadataModel                       $taskMetadataModel
 * @property \Kanboard\Model\ThemeModel                              $themeModel
 * @property \Kanboard\Model\TimezoneModel                           $timezoneModel
 * @property \Kanboard\Model\TransitionModel                         $transitionModel
 * @property \Kanboard\Model\UserModel                               $userModel
 * @property \Kanboard\Model\UserLockingModel                        $userLockingModel
 * @property \Kanboard\Model\UserNotificationModel                   $userNotificationModel
 * @property \Kanboard\Model\UserNotificationTypeModel               $userNotificationTypeModel
 * @property \Kanboard\Model\UserNotificationFilterModel             $userNotificationFilterModel
 * @property \Kanboard\Model\UserUnreadNotificationModel             $userUnreadNotificationModel
 * @property \Kanboard\Model\UserMetadataModel                       $userMetadataModel
 * @property \Kanboard\Pagination\DashboardPagination                $dashboardPagination
 * @property \Kanboard\Pagination\ProjectPagination                  $projectPagination
 * @property \Kanboard\Pagination\TaskPagination                     $taskPagination
 * @property \Kanboard\Pagination\SubtaskPagination                  $subtaskPagination
 * @property \Kanboard\Pagination\UserPagination                     $userPagination
 * @property \Kanboard\Validator\ActionValidator                     $actionValidator
 * @property \Kanboard\Validator\AuthValidator                       $authValidator
 * @property \Kanboard\Validator\ColumnValidator                     $columnValidator
 * @property \Kanboard\Validator\CategoryValidator                   $categoryValidator
 * @property \Kanboard\Validator\ColumnRestrictionValidator          $columnRestrictionValidator
 * @property \Kanboard\Validator\ColumnMoveRestrictionValidator      $columnMoveRestrictionValidator
 * @property \Kanboard\Validator\CommentValidator                    $commentValidator
 * @property \Kanboard\Validator\CurrencyValidator                   $currencyValidator
 * @property \Kanboard\Validator\CustomFilterValidator               $customFilterValidator
 * @property \Kanboard\Validator\ExternalLinkValidator               $externalLinkValidator
 * @property \Kanboard\Validator\GroupValidator                      $groupValidator
 * @property \Kanboard\Validator\LinkValidator                       $linkValidator
 * @property \Kanboard\Validator\PasswordResetValidator              $passwordResetValidator
 * @property \Kanboard\Validator\ProjectValidator                    $projectValidator
 * @property \Kanboard\Validator\ProjectRoleValidator                $projectRoleValidator
 * @property \Kanboard\Validator\SubtaskValidator                    $subtaskValidator
 * @property \Kanboard\Validator\SwimlaneValidator                   $swimlaneValidator
 * @property \Kanboard\Validator\TagValidator                        $tagValidator
 * @property \Kanboard\Validator\TaskLinkValidator                   $taskLinkValidator
 * @property \Kanboard\Validator\TaskValidator                       $taskValidator
 * @property \Kanboard\Validator\UserValidator                       $userValidator
 * @property \Kanboard\Validator\PredefinedTaskDescriptionValidator  $predefinedTaskDescriptionValidator
 * @property \Kanboard\Import\UserImport                             $userImport
 * @property \Kanboard\Export\SubtaskExport                          $subtaskExport
 * @property \Kanboard\Export\TaskExport                             $taskExport
 * @property \Kanboard\Export\TransitionExport                       $transitionExport
 * @property \Kanboard\Core\Filter\QueryBuilder                      $projectGroupRoleQuery
 * @property \Kanboard\Core\Filter\QueryBuilder                      $projectUserRoleQuery
 * @property \Kanboard\Core\Filter\QueryBuilder                      $projectActivityQuery
 * @property \Kanboard\Core\Filter\QueryBuilder                      $userQuery
 * @property \Kanboard\Core\Filter\QueryBuilder                      $projectQuery
 * @property \Kanboard\Core\Filter\QueryBuilder                      $taskQuery
 * @property \Kanboard\Core\Filter\LexerBuilder                      $taskLexer
 * @property \Kanboard\Core\Filter\LexerBuilder                      $projectActivityLexer
 * @property \Kanboard\Job\CommentEventJob                           $commentEventJob
 * @property \Kanboard\Job\SubtaskEventJob                           $subtaskEventJob
 * @property \Kanboard\Job\TaskEventJob                              $taskEventJob
 * @property \Kanboard\Job\TaskFileEventJob                          $taskFileEventJob
 * @property \Kanboard\Job\TaskLinkEventJob                          $taskLinkEventJob
 * @property \Kanboard\Job\ProjectFileEventJob                       $projectFileEventJob
 * @property \Kanboard\Job\NotificationJob                           $notificationJob
 * @property \Kanboard\Job\ProjectMetricJob                          $projectMetricJob
 * @property \Kanboard\Job\UserMentionJob                            $userMentionJob
 * @property \Psr\Log\LoggerInterface                                $logger
 * @property \PicoDb\Database                                        $db
 * @property \Symfony\Component\EventDispatcher\EventDispatcher      $dispatcher
 * @property \Symfony\Component\Console\Application                  $cli
 * @property \JsonRPC\Server                                         $api
 */
abstract class Base
{
    /**
     * Container instance
     *
     * @access protected
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * Constructor
     *
     * @access public
     * @param  \Pimple\Container   $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Load automatically dependencies
     *
     * @access public
     * @param  string $name Class name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->container[$name];
    }

    /**
     * Get object instance
     *
     * @static
     * @access public
     * @param  Container $container
     * @return static
     */
    public static function getInstance(Container $container)
    {
        return new static($container);
    }
}
