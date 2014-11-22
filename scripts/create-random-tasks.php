#!/usr/bin/env php
<?php

require __DIR__.'/../app/common.php';

use Model\TaskCreation;
use Model\SubTask;
use Model\Project;
use Model\ProjectPermission;
use Model\User;

$task_per_column = 50;

$userModel = new User($container);
$projectModel = new Project($container);
$permissionModel = new ProjectPermission($container);
$taskModel = new TaskCreation($container);
$subtaskModel = new SubTask($container);

for ($i = 0; $i <= 100; $i++) {
    $id = $projectModel->create(array(
        'name' => 'Project #'.$i
    ));

    $permissionModel->allowUser($id, 1);
}

for ($i = 0; $i <= 500; $i++) {
    $userModel->create(array(
        'username' => 'user'.$i,
        'password' => 'password'.$i,
        'name' => 'User #'.$i,
        'email' => 'user'.$i.'@localhost',
    ));
}

foreach (array(1, 2, 3, 4) as $column_id) {

    for ($i = 1; $i <= $task_per_column; $i++) {

        $task = array(
            'title' => 'Task #'.$i.'-'.$column_id,
            'project_id' => mt_rand(1, 100),
            'column_id' => $column_id,
            'owner_id' => 1,
            'color_id' => mt_rand(0, 1) === 0 ? 'green' : 'purple',
            'score' => mt_rand(0, 21),
            'is_active' => mt_rand(0, 1),
        );

        $id = $taskModel->create($task);

        $subtaskModel->create(array(
            'title' => 'Subtask of task #'.$id,
            'user_id' => 1,
            'status' => mt_rand(0, 2),
            'task_id' => $id,
        ));
    }
}
