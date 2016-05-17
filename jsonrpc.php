<?php

require __DIR__.'/app/common.php';

use JsonRPC\Server;
use Kanboard\Api\AuthApi;
use Kanboard\Api\MeApi;
use Kanboard\Api\ActionApi;
use Kanboard\Api\AppApi;
use Kanboard\Api\BoardApi;
use Kanboard\Api\ColumnApi;
use Kanboard\Api\CategoryApi;
use Kanboard\Api\CommentApi;
use Kanboard\Api\FileApi;
use Kanboard\Api\LinkApi;
use Kanboard\Api\ProjectApi;
use Kanboard\Api\ProjectPermissionApi;
use Kanboard\Api\SubtaskApi;
use Kanboard\Api\SwimlaneApi;
use Kanboard\Api\TaskApi;
use Kanboard\Api\TaskLinkApi;
use Kanboard\Api\UserApi;
use Kanboard\Api\GroupApi;
use Kanboard\Api\GroupMemberApi;

$server = new Server;
$server->setAuthenticationHeader(API_AUTHENTICATION_HEADER);
$server->before(array(new AuthApi($container), 'checkCredentials'));

$server->attach(new MeApi($container));
$server->attach(new ActionApi($container));
$server->attach(new AppApi($container));
$server->attach(new BoardApi($container));
$server->attach(new ColumnApi($container));
$server->attach(new CategoryApi($container));
$server->attach(new CommentApi($container));
$server->attach(new FileApi($container));
$server->attach(new LinkApi($container));
$server->attach(new ProjectApi($container));
$server->attach(new ProjectPermissionApi($container));
$server->attach(new SubtaskApi($container));
$server->attach(new SwimlaneApi($container));
$server->attach(new TaskApi($container));
$server->attach(new TaskLinkApi($container));
$server->attach(new UserApi($container));
$server->attach(new GroupApi($container));
$server->attach(new GroupMemberApi($container));

echo $server->execute();
