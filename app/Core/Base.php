<?php

namespace Kanboard\Core;

use Pimple\Container;

/**
 * Base class
 *
 * @package core
 * @author  Frederic Guillot
 *
 * @property \Kanboard\Core\Helper                                      $helper
 * @property \Kanboard\Core\Mail\Client                                 $emailClient
 * @property \Kanboard\Core\Paginator                                   $paginator
 * @property \Kanboard\Core\Http\Client                                 $httpClient
 * @property \Kanboard\Core\Http\Request                                $request
 * @property \Kanboard\Core\Http\Router                                 $router
 * @property \Kanboard\Core\Http\Response                               $response
 * @property \Kanboard\Core\Session                                     $session
 * @property \Kanboard\Core\Template                                    $template
 * @property \Kanboard\Core\OAuth2                                      $oauth
 * @property \Kanboard\Core\Lexer                                       $lexer
 * @property \Kanboard\Core\ObjectStorage\ObjectStorageInterface        $objectStorage
 * @property \Kanboard\Core\Cache\Cache                                 $memoryCache
 * @property \Kanboard\Core\Plugin\Hook                                 $hook
 * @property \Kanboard\Core\Plugin\Loader                               $pluginLoader
 * @property \Kanboard\Core\Security\Token                              $token
 * @property \Kanboard\Integration\BitbucketWebhook                     $bitbucketWebhook
 * @property \Kanboard\Integration\GithubWebhook                        $githubWebhook
 * @property \Kanboard\Integration\GitlabWebhook                        $gitlabWebhook
 * @property \Kanboard\Formatter\ProjectGanttFormatter                  $projectGanttFormatter
 * @property \Kanboard\Formatter\TaskFilterGanttFormatter               $taskFilterGanttFormatter
 * @property \Kanboard\Formatter\TaskFilterAutoCompleteFormatter        $taskFilterAutoCompleteFormatter
 * @property \Kanboard\Formatter\TaskFilterCalendarFormatter            $taskFilterCalendarFormatter
 * @property \Kanboard\Formatter\TaskFilterICalendarFormatter           $taskFilterICalendarFormatter
 * @property \Kanboard\Model\Acl                                        $acl
 * @property \Kanboard\Model\Action                                     $action
 * @property \Kanboard\Model\Authentication                             $authentication
 * @property \Kanboard\Model\Board                                      $board
 * @property \Kanboard\Model\Category                                   $category
 * @property \Kanboard\Model\Color                                      $color
 * @property \Kanboard\Model\Comment                                    $comment
 * @property \Kanboard\Model\Config                                     $config
 * @property \Kanboard\Model\Currency                                   $currency
 * @property \Kanboard\Model\CustomFilter                               $customFilter
 * @property \Kanboard\Model\DateParser                                 $dateParser
 * @property \Kanboard\Model\File                                       $file
 * @property \Kanboard\Model\LastLogin                                  $lastLogin
 * @property \Kanboard\Model\Link                                       $link
 * @property \Kanboard\Model\Notification                               $notification
 * @property \Kanboard\Model\OverdueNotification                        $overdueNotification
 * @property \Kanboard\Model\Project                                    $project
 * @property \Kanboard\Model\ProjectActivity                            $projectActivity
 * @property \Kanboard\Model\ProjectAnalytic                            $projectAnalytic
 * @property \Kanboard\Model\ProjectDuplication                         $projectDuplication
 * @property \Kanboard\Model\ProjectDailyColumnStats                    $projectDailyColumnStats
 * @property \Kanboard\Model\ProjectDailyStats                          $projectDailyStats
 * @property \Kanboard\Model\ProjectMetadata                            $projectMetadata
 * @property \Kanboard\Model\ProjectPermission                          $projectPermission
 * @property \Kanboard\Model\ProjectNotification                        $projectNotification
 * @property \Kanboard\Model\ProjectNotificationType                    $projectNotificationType
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
 * @property \Kanboard\Model\TaskValidator                              $taskValidator
 * @property \Kanboard\Model\TaskMetadata                               $taskMetadata
 * @property \Kanboard\Model\Transition                                 $transition
 * @property \Kanboard\Model\User                                       $user
 * @property \Kanboard\Model\UserImport                                 $userImport
 * @property \Kanboard\Model\UserNotification                           $userNotification
 * @property \Kanboard\Model\UserNotificationType                       $userNotificationType
 * @property \Kanboard\Model\UserNotificationFilter                     $userNotificationFilter
 * @property \Kanboard\Model\UserUnreadNotification                     $userUnreadNotification
 * @property \Kanboard\Model\UserSession                                $userSession
 * @property \Kanboard\Model\UserMetadata                               $userMetadata
 * @property \Kanboard\Model\Webhook                                    $webhook
 * @property \Psr\Log\LoggerInterface                                   $logger
 * @property \League\HTMLToMarkdown\HtmlConverter                       $htmlConverter
 * @property \PicoDb\Database                                           $db
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
