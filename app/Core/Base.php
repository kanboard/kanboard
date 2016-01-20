<?php

namespace Kanboard\Core;

use Pimple\Container;

/**
 * Base Class
 *
 * @package core
 * @author  Frederic Guillot
 *
 * @property \Kanboard\Analytic\TaskDistributionAnalytic                $taskDistributionAnalytic
 * @property \Kanboard\Analytic\UserDistributionAnalytic                $userDistributionAnalytic
 * @property \Kanboard\Analytic\EstimatedTimeComparisonAnalytic         $estimatedTimeComparisonAnalytic
 * @property \Kanboard\Analytic\AverageLeadCycleTimeAnalytic            $averageLeadCycleTimeAnalytic
 * @property \Kanboard\Analytic\AverageTimeSpentColumnAnalytic          $averageTimeSpentColumnAnalytic
 * @property \Kanboard\Core\Action\ActionManager                        $actionManager
 * @property \Kanboard\Core\Cache\MemoryCache                           $memoryCache
 * @property \Kanboard\Core\Event\EventManager                          $eventManager
 * @property \Kanboard\Core\Group\GroupManager                          $groupManager
 * @property \Kanboard\Core\Http\Client                                 $httpClient
 * @property \Kanboard\Core\Http\OAuth2                                 $oauth
 * @property \Kanboard\Core\Http\RememberMeCookie                       $rememberMeCookie
 * @property \Kanboard\Core\Http\Request                                $request
 * @property \Kanboard\Core\Http\Response                               $response
 * @property \Kanboard\Core\Http\Router                                 $router
 * @property \Kanboard\Core\Http\Route                                  $route
 * @property \Kanboard\Core\Mail\Client                                 $emailClient
 * @property \Kanboard\Core\ObjectStorage\ObjectStorageInterface        $objectStorage
 * @property \Kanboard\Core\Plugin\Hook                                 $hook
 * @property \Kanboard\Core\Plugin\Loader                               $pluginLoader
 * @property \Kanboard\Core\Security\AccessMap                          $projectAccessMap
 * @property \Kanboard\Core\Security\AuthenticationManager              $authenticationManager
 * @property \Kanboard\Core\Security\AccessMap                          $applicationAccessMap
 * @property \Kanboard\Core\Security\AccessMap                          $projectAccessMap
 * @property \Kanboard\Core\Security\Authorization                      $applicationAuthorization
 * @property \Kanboard\Core\Security\Authorization                      $projectAuthorization
 * @property \Kanboard\Core\Security\Role                               $role
 * @property \Kanboard\Core\Security\Token                              $token
 * @property \Kanboard\Core\Session\FlashMessage                        $flash
 * @property \Kanboard\Core\Session\SessionManager                      $sessionManager
 * @property \Kanboard\Core\Session\SessionStorage                      $sessionStorage
 * @property \Kanboard\Core\User\GroupSync                              $groupSync
 * @property \Kanboard\Core\User\UserProfile                            $userProfile
 * @property \Kanboard\Core\User\UserSync                               $userSync
 * @property \Kanboard\Core\User\UserSession                            $userSession
 * @property \Kanboard\Core\DateParser                                  $dateParser
 * @property \Kanboard\Core\Helper                                      $helper
 * @property \Kanboard\Core\Lexer                                       $lexer
 * @property \Kanboard\Core\Paginator                                   $paginator
 * @property \Kanboard\Core\Template                                    $template
 * @property \Kanboard\Formatter\ProjectGanttFormatter                  $projectGanttFormatter
 * @property \Kanboard\Formatter\TaskFilterGanttFormatter               $taskFilterGanttFormatter
 * @property \Kanboard\Formatter\TaskFilterAutoCompleteFormatter        $taskFilterAutoCompleteFormatter
 * @property \Kanboard\Formatter\TaskFilterCalendarFormatter            $taskFilterCalendarFormatter
 * @property \Kanboard\Formatter\TaskFilterICalendarFormatter           $taskFilterICalendarFormatter
 * @property \Kanboard\Formatter\UserFilterAutoCompleteFormatter        $userFilterAutoCompleteFormatter
 * @property \Kanboard\Formatter\GroupAutoCompleteFormatter             $groupAutoCompleteFormatter
 * @property \Kanboard\Model\Action                                     $action
 * @property \Kanboard\Model\ActionParameter                            $actionParameter
 * @property \Kanboard\Model\Board                                      $board
 * @property \Kanboard\Model\Category                                   $category
 * @property \Kanboard\Model\Color                                      $color
 * @property \Kanboard\Model\Comment                                    $comment
 * @property \Kanboard\Model\Config                                     $config
 * @property \Kanboard\Model\Currency                                   $currency
 * @property \Kanboard\Model\CustomFilter                               $customFilter
 * @property \Kanboard\Model\File                                       $file
 * @property \Kanboard\Model\Group                                      $group
 * @property \Kanboard\Model\GroupMember                                $groupMember
 * @property \Kanboard\Model\LastLogin                                  $lastLogin
 * @property \Kanboard\Model\Link                                       $link
 * @property \Kanboard\Model\Notification                               $notification
 * @property \Kanboard\Model\OverdueNotification                        $overdueNotification
 * @property \Kanboard\Model\PasswordReset                              $passwordReset
 * @property \Kanboard\Model\Project                                    $project
 * @property \Kanboard\Model\ProjectActivity                            $projectActivity
 * @property \Kanboard\Model\ProjectAnalytic                            $projectAnalytic
 * @property \Kanboard\Model\ProjectDuplication                         $projectDuplication
 * @property \Kanboard\Model\ProjectDailyColumnStats                    $projectDailyColumnStats
 * @property \Kanboard\Model\ProjectDailyStats                          $projectDailyStats
 * @property \Kanboard\Model\ProjectMetadata                            $projectMetadata
 * @property \Kanboard\Model\ProjectPermission                          $projectPermission
 * @property \Kanboard\Model\ProjectUserRole                            $projectUserRole
 * @property \Kanboard\Model\projectUserRoleFilter                      $projectUserRoleFilter
 * @property \Kanboard\Model\ProjectGroupRole                           $projectGroupRole
 * @property \Kanboard\Model\ProjectNotification                        $projectNotification
 * @property \Kanboard\Model\ProjectNotificationType                    $projectNotificationType
 * @property \Kanboard\Model\RememberMeSession                          $rememberMeSession
 * @property \Kanboard\Model\Subtask                                    $subtask
 * @property \Kanboard\Model\SubtaskExport                              $subtaskExport
 * @property \Kanboard\Model\SubtaskTimeTracking                        $subtaskTimeTracking
 * @property \Kanboard\Model\Swimlane                                   $swimlane
 * @property \Kanboard\Model\Task                                       $task
 * @property \Kanboard\Model\TaskAnalytic                               $taskAnalytic
 * @property \Kanboard\Model\TaskCreation                               $taskCreation
 * @property \Kanboard\Model\TaskDuplication                            $taskDuplication
 * @property \Kanboard\Model\TaskExport                                 $taskExport
 * @property \Kanboard\Model\TaskImport                                 $taskImport
 * @property \Kanboard\Model\TaskFinder                                 $taskFinder
 * @property \Kanboard\Model\TaskFilter                                 $taskFilter
 * @property \Kanboard\Model\TaskLink                                   $taskLink
 * @property \Kanboard\Model\TaskModification                           $taskModification
 * @property \Kanboard\Model\TaskPermission                             $taskPermission
 * @property \Kanboard\Model\TaskPosition                               $taskPosition
 * @property \Kanboard\Model\TaskStatus                                 $taskStatus
 * @property \Kanboard\Model\TaskMetadata                               $taskMetadata
 * @property \Kanboard\Model\Transition                                 $transition
 * @property \Kanboard\Model\User                                       $user
 * @property \Kanboard\Model\UserImport                                 $userImport
 * @property \Kanboard\Model\UserLocking                                $userLocking
 * @property \Kanboard\Model\UserMention                                $userMention
 * @property \Kanboard\Model\UserNotification                           $userNotification
 * @property \Kanboard\Model\UserNotificationType                       $userNotificationType
 * @property \Kanboard\Model\UserNotificationFilter                     $userNotificationFilter
 * @property \Kanboard\Model\UserUnreadNotification                     $userUnreadNotification
 * @property \Kanboard\Model\UserMetadata                               $userMetadata
 * @property \Kanboard\Model\Webhook                                    $webhook
 * @property \Kanboard\Validator\ActionValidator                        $actionValidator
 * @property \Kanboard\Validator\AuthValidator                          $authValidator
 * @property \Kanboard\Validator\ColumnValidator                        $columnValidator
 * @property \Kanboard\Validator\CategoryValidator                      $categoryValidator
 * @property \Kanboard\Validator\ColumnValidator                        $columnValidator
 * @property \Kanboard\Validator\CommentValidator                       $commentValidator
 * @property \Kanboard\Validator\CurrencyValidator                      $currencyValidator
 * @property \Kanboard\Validator\CustomFilterValidator                  $customFilterValidator
 * @property \Kanboard\Validator\GroupValidator                         $groupValidator
 * @property \Kanboard\Validator\LinkValidator                          $linkValidator
 * @property \Kanboard\Validator\PasswordResetValidator                 $passwordResetValidator
 * @property \Kanboard\Validator\ProjectValidator                       $projectValidator
 * @property \Kanboard\Validator\SubtaskValidator                       $subtaskValidator
 * @property \Kanboard\Validator\SwimlaneValidator                      $swimlaneValidator
 * @property \Kanboard\Validator\TaskLinkValidator                      $taskLinkValidator
 * @property \Kanboard\Validator\TaskValidator                          $taskValidator
 * @property \Kanboard\Validator\UserValidator                          $userValidator
 * @property \Psr\Log\LoggerInterface                                   $logger
 * @property \PicoDb\Database                                           $db
 * @property \Symfony\Component\EventDispatcher\EventDispatcher         $dispatcher
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
     * Load automatically models
     *
     * @access public
     * @param  string $name Model name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->container[$name];
    }
}
