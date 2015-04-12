#!/usr/bin/env php
<?php

require __DIR__.'/../app/common.php';

use Model\ProjectDailySummary;
use Model\TaskCreation;
use Model\TaskStatus;

$pds = new ProjectDailySummary($container);
$taskCreation = new TaskCreation($container);
$taskStatus = new TaskStatus($container);

for ($i = 1; $i <= 15; $i++) {

    $task = array(
        'title' => 'Task #'.$i,
        'project_id' => 1,
        'column_id' => rand(1, 4),
        'score' => rand(1, 21)
    );

    $taskCreation->create($task);
}

$pds->updateTotals(1, date('Y-m-d', strtotime('-7 days')));

$taskStatus->close(1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-6 days')));

$taskStatus->close(2);
$taskStatus->close(3);
$pds->updateTotals(1, date('Y-m-d', strtotime('-5 days')));

$taskStatus->close(4);
$pds->updateTotals(1, date('Y-m-d', strtotime('-4 days')));

$taskStatus->close(5);
$pds->updateTotals(1, date('Y-m-d', strtotime('-3 days')));

$taskStatus->close(6);
$taskStatus->close(7);
$taskStatus->close(8);
$pds->updateTotals(1, date('Y-m-d', strtotime('-2 days')));

$taskStatus->close(9);
$taskStatus->close(10);
$pds->updateTotals(1, date('Y-m-d', strtotime('-2 days')));

$taskStatus->close(12);
$taskStatus->close(13);
$pds->updateTotals(1, date('Y-m-d', strtotime('-1 days')));

$taskStatus->close(1);
$pds->updateTotals(1, date('Y-m-d'));