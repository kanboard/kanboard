#!/usr/bin/env php
<?php

require __DIR__.'/../app/common.php';

use Model\Project;
use Model\ProjectDailyStats;

$p = new Project($container);
$pds = new ProjectDailyStats($container);

$p->create(array('name' => 'Test Lead/Cycle time'));

$container['db']->table('tasks')->insert(array(
    'title' => 'Lead time = 4d | Cycle time = 3d',
    'date_creation' => strtotime('-7 days'),
    'date_started' => strtotime('-6 days'),
    'date_completed' => strtotime('-3 days'),
    'is_active' => 0,
    'project_id' => 1,
    'column_id' => 1,
));

$container['db']->table('tasks')->insert(array(
    'title' => 'Lead time = 1d | Cycle time = 1d',
    'date_creation' => strtotime('-7 days'),
    'date_started' => strtotime('-7 days'),
    'date_completed' => strtotime('-6 days'),
    'is_active' => 0,
    'project_id' => 1,
    'column_id' => 1,
));

$pds->updateTotals(1, date('Y-m-d', strtotime('-6 days')));

$container['db']->table('tasks')->insert(array(
    'title' => 'Lead time = 7d | Cycle time = 5d',
    'date_creation' => strtotime('-7 days'),
    'date_started' => strtotime('-5 days'),
    'date_completed' => strtotime('today'),
    'is_active' => 0,
    'project_id' => 1,
    'column_id' => 1,
));

$pds->updateTotals(1, date('Y-m-d', strtotime('-5 days')));

$container['db']->table('tasks')->insert(array(
    'title' => 'Lead time = 1d | Cycle time = 0',
    'date_creation' => strtotime('-3 days'),
    'date_started' => 0,
    'date_completed' => 0,
    'is_active' => 0,
    'project_id' => 1,
    'column_id' => 1,
));

$pds->updateTotals(1, date('Y-m-d', strtotime('-4 days')));

$container['db']->table('tasks')->insert(array(
    'title' => 'Lead time = 1d | Cycle time = 1d',
    'date_creation' => strtotime('-3 days'),
    'date_started' => strtotime('-3 days'),
    'date_completed' => 0,
    'is_active' => 0,
    'project_id' => 1,
    'column_id' => 1,
));

$pds->updateTotals(1, date('Y-m-d', strtotime('-3 days')));
