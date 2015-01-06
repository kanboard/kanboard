#!/usr/bin/env php
<?php

require __DIR__.'/../app/common.php';

use Model\ProjectDailySummary;
use Model\TaskCreation;
use Model\TaskPosition;

$pds = new ProjectDailySummary($container);
$taskCreation = new TaskCreation($container);
$taskPosition = new TaskPosition($container);

for ($i = 1; $i <= 15; $i++) {

    $task = array(
        'title' => 'Task #'.$i,
        'project_id' => 1,
        'column_id' => 1,
    );

    $taskCreation->create($task);
}

$pds->updateTotals(1, date('Y-m-d', strtotime('-7 days')));

$taskPosition->movePosition(1, 1, 2, 1);
$taskPosition->movePosition(1, 2, 2, 1);
$taskPosition->movePosition(1, 3, 2, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-6 days')));

$taskPosition->movePosition(1, 3, 3, 1);
$taskPosition->movePosition(1, 4, 3, 1);
$taskPosition->movePosition(1, 5, 3, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-5 days')));

$taskPosition->movePosition(1, 5, 4, 1);
$taskPosition->movePosition(1, 6, 4, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-4 days')));

$taskPosition->movePosition(1, 7, 4, 1);
$taskPosition->movePosition(1, 8, 4, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-3 days')));

$taskPosition->movePosition(1, 9, 3, 1);
$taskPosition->movePosition(1, 10, 2, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-2 days')));

$taskCreation->create(array('title' => 'Random task', 'project_id' => 1));
$taskPosition->movePosition(1, 11, 2, 1);
$taskPosition->movePosition(1, 12, 4, 1);
$taskPosition->movePosition(1, 13, 4, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-2 days')));

$taskPosition->movePosition(1, 14, 3, 1);
$pds->updateTotals(1, date('Y-m-d', strtotime('-1 days')));

$taskPosition->movePosition(1, 15, 4, 1);
$taskPosition->movePosition(1, 16, 4, 1);

$taskCreation->create(array('title' => 'Random task', 'project_id' => 1));

$pds->updateTotals(1, date('Y-m-d'));