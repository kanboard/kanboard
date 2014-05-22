<?php

if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    require __DIR__.'/../vendor/password.php';
}

require_once __DIR__.'/../vendor/PicoDb/Database.php';
require_once __DIR__.'/../core/event.php';
require_once __DIR__.'/../core/translator.php';
require_once __DIR__.'/../schemas/sqlite.php';
require_once __DIR__.'/../models/task.php';
require_once __DIR__.'/../models/acl.php';
require_once __DIR__.'/../models/comment.php';
require_once __DIR__.'/../models/project.php';
require_once __DIR__.'/../models/user.php';
require_once __DIR__.'/../models/board.php';
require_once __DIR__.'/../models/action.php';
require_once __DIR__.'/../actions/task_close.php';
require_once __DIR__.'/../actions/task_assign_specific_user.php';
require_once __DIR__.'/../actions/task_assign_color_user.php';
require_once __DIR__.'/../actions/task_assign_current_user.php';
require_once __DIR__.'/../actions/task_duplicate_another_project.php';

abstract class Base extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->db = $this->getDbConnection();
        $this->event = new \Core\Event;
    }

    public function getDbConnection()
    {
        $db = new \PicoDb\Database(array(
            'driver' => 'sqlite',
            'filename' => ':memory:'
        ));

        if ($db->schema()->check(16)) {
            return $db;
        }
        else {
            die('Unable to migrate database schema!');
        }
    }
}
