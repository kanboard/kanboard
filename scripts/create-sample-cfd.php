#!/usr/bin/env php
<?php

require __DIR__.'/../app/common.php';

use Model\ProjectDailySummary;
use Model\Task;

$pds = new ProjectDailySummary($container);
$taskModel = new Task($container);

for ($i = 1; $i <= 15; $i++) {

    $task = array(
        'title' => 'Task #'.$i,
        'project_id' => 1,
        'column_id' => 1,
    );

    $taskModel->create($task);
}

$pds->updateTotals(1, date('Y-m-d', strtotime('-7 days')));

$taskModel->movePosition(1, 1, 2, 1);
$taskModel->movePosition(1, 2, 2, 1);
$taskModel->movePosition(1, 3, 2, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-6 days')));

$taskModel->movePosition(1, 3, 3, 1);
$taskModel->movePosition(1, 4, 3, 1);
$taskModel->movePosition(1, 5, 3, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-5 days')));

$taskModel->movePosition(1, 5, 4, 1);
$taskModel->movePosition(1, 6, 4, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-4 days')));

$taskModel->movePosition(1, 7, 4, 1);
$taskModel->movePosition(1, 8, 4, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-3 days')));

$taskModel->movePosition(1, 9, 3, 1);
$taskModel->movePosition(1, 10, 2, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-2 days')));

$taskModel->create(array('title' => 'Random task', 'project_id' => 1));
$taskModel->movePosition(1, 11, 2, 1);
$taskModel->movePosition(1, 12, 4, 1);
$taskModel->movePosition(1, 13, 4, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-2 days')));

$taskModel->movePosition(1, 14, 3, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-1 days')));

$taskModel->movePosition(1, 15, 4, 1);
$taskModel->movePosition(1, 16, 4, 1);

$taskModel->create(array('title' => 'Random task', 'project_id' => 1));

$pds->updateTotals(1, date('Y-m-d'));