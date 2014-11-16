#!/usr/bin/env php
<?php

require __DIR__.'/../app/common.php';

use Model\User;

$userModel = new User($container);

for ($i = 0; $i < 500; $i++) {
    $userModel->create(array(
        'username' => 'user'.$i,
        'password' => 'password'.$i,
        'name' => 'User #'.$i,
        'email' => 'user'.$i.'@localhost',
    ));
}
