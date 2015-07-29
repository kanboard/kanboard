<?php

require __DIR__.'/app/common.php';

$server = new JsonRPC\Server;
$server->setAuthenticationHeader(API_AUTHENTICATION_HEADER);
$server->before(array(new Api\Auth($container), 'checkCredentials'));

$server->attach(new Api\Me($container));
$server->attach(new Api\Action($container));
$server->attach(new Api\App($container));
$server->attach(new Api\Board($container));
$server->attach(new Api\Category($container));
$server->attach(new Api\Comment($container));
$server->attach(new Api\File($container));
$server->attach(new Api\Link($container));
$server->attach(new Api\Project($container));
$server->attach(new Api\ProjectPermission($container));
$server->attach(new Api\Subtask($container));
$server->attach(new Api\Swimlane($container));
$server->attach(new Api\Task($container));
$server->attach(new Api\TaskLink($container));
$server->attach(new Api\User($container));

echo $server->execute();
