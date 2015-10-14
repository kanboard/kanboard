<?php

require __DIR__.'/app/common.php';

use JsonRPC\Server;
use Kanboard\Api\Auth;
use Kanboard\Api\Me;
use Kanboard\Api\Action;
use Kanboard\Api\App;
use Kanboard\Api\Board;
use Kanboard\Api\Category;
use Kanboard\Api\Comment;
use Kanboard\Api\File;
use Kanboard\Api\Link;
use Kanboard\Api\Project;
use Kanboard\Api\ProjectPermission;
use Kanboard\Api\Subtask;
use Kanboard\Api\Swimlane;
use Kanboard\Api\Task;
use Kanboard\Api\TaskLink;
use Kanboard\Api\User;

$server = new Server;
$server->setAuthenticationHeader(API_AUTHENTICATION_HEADER);
$server->before(array(new Auth($container), 'checkCredentials'));

$server->attach(new Me($container));
$server->attach(new Action($container));
$server->attach(new App($container));
$server->attach(new Board($container));
$server->attach(new Category($container));
$server->attach(new Comment($container));
$server->attach(new File($container));
$server->attach(new Link($container));
$server->attach(new Project($container));
$server->attach(new ProjectPermission($container));
$server->attach(new Subtask($container));
$server->attach(new Swimlane($container));
$server->attach(new Task($container));
$server->attach(new TaskLink($container));
$server->attach(new User($container));

echo $server->execute();
