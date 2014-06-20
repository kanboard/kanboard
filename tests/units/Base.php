<?php

if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    require __DIR__.'/../../vendor/password.php';
}

require_once __DIR__.'/../../app/Core/Security.php';

require_once __DIR__.'/../../vendor/PicoDb/Database.php';
require_once __DIR__.'/../../app/Schema/Sqlite.php';

require_once __DIR__.'/../../app/Core/Listener.php';
require_once __DIR__.'/../../app/Core/Event.php';
require_once __DIR__.'/../../app/Core/Translator.php';
require_once __DIR__.'/../../app/translator.php';

require_once __DIR__.'/../../app/Model/Base.php';
require_once __DIR__.'/../../app/Model/Task.php';
require_once __DIR__.'/../../app/Model/Acl.php';
require_once __DIR__.'/../../app/Model/Comment.php';
require_once __DIR__.'/../../app/Model/Project.php';
require_once __DIR__.'/../../app/Model/User.php';
require_once __DIR__.'/../../app/Model/Board.php';
require_once __DIR__.'/../../app/Model/Action.php';
require_once __DIR__.'/../../app/Model/Category.php';

require_once __DIR__.'/../../app/Action/Base.php';
require_once __DIR__.'/../../app/Action/TaskClose.php';
require_once __DIR__.'/../../app/Action/TaskAssignSpecificUser.php';
require_once __DIR__.'/../../app/Action/TaskAssignColorUser.php';
require_once __DIR__.'/../../app/Action/TaskAssignColorCategory.php';
require_once __DIR__.'/../../app/Action/TaskAssignCurrentUser.php';
require_once __DIR__.'/../../app/Action/TaskDuplicateAnotherProject.php';

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

        if ($db->schema()->check(\Schema\VERSION)) {
            return $db;
        }
        else {
            die('Unable to migrate database schema!');
        }
    }
}
