#!/usr/bin/env php
<?php

require __DIR__.'/../app/common.php';

use Model\Task;

$task_per_column = 250;
$taskModel = new Task($registry);

foreach (array(1, 2, 3, 4) as $column_id) {

    for ($i = 1; $i <= $task_per_column; $i++) {

        $task = array(
            'title' => 'Task #'.$i.'-'.$column_id,
            'project_id' => 1,
            'column_id' => $column_id,
            'owner_id' => rand(0, 1),
            'color_id' => rand(0, 1) === 0 ? 'green' : 'purple',
            'score' => rand(0, 21),
            'is_active' => rand(0, 1),
        );

        $taskModel->create($task);
    }
}
