#!/usr/bin/env php
<?php

require __DIR__.'/../app/common.php';

use Model\Project;
use Model\ProjectPermission;

$projectModel = new Project($container);
$permissionModel = new ProjectPermission($container);

for ($i = 0; $i < 100; $i++) {
    $id = $projectModel->create(array(
        'name' => 'Project #'.$i
    ));

    $permissionModel->allowUser($id, 1);
}
