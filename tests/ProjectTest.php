<?php

require_once __DIR__.'/../lib/translator.php';
require_once __DIR__.'/../models/base.php';
require_once __DIR__.'/../models/board.php';
require_once __DIR__.'/../models/user.php';
require_once __DIR__.'/../models/project.php';

use Model\Project;
use Model\User;

class ProjectTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        defined('DB_FILENAME') or define('DB_FILENAME', ':memory:');
    }

    public function testCreation()
    {
        $p = new Project;
        $this->assertEquals(1, $p->create(array('name' => 'UnitTest')));
        $this->assertNotEmpty($p->getById(1));
    }

    public function testAllowUsers()
    {
        $p = new Project;

        // Everybody is allowed
        $this->assertEmpty($p->getAllowedUsers(1));
        $this->assertTrue($p->isUserAllowed(1, 1));

        // Allow one user
        $this->assertTrue($p->allowUser(1, 1));
        $this->assertFalse($p->allowUser(50, 1));
        $this->assertFalse($p->allowUser(1, 50));
        $this->assertEquals(array('1' => 'admin'), $p->getAllowedUsers(1));
        $this->assertTrue($p->isUserAllowed(1, 1));

        // Disallow one user
        $this->assertTrue($p->revokeUser(1, 1));
        $this->assertEmpty($p->getAllowedUsers(1));
        $this->assertTrue($p->isUserAllowed(1, 1));

        // Allow/disallow many users
        $user = new User;
        $user->create(array('username' => 'unittest', 'password' => 'unittest'));

        $this->assertTrue($p->allowUser(1, 1));
        $this->assertTrue($p->allowUser(1, 2));

        $this->assertEquals(array('1' => 'admin', '2' => 'unittest'), $p->getAllowedUsers(1));
        $this->assertTrue($p->isUserAllowed(1, 1));
        $this->assertTrue($p->isUserAllowed(1, 2));

        $this->assertTrue($p->revokeUser(1, 1));

        $this->assertEquals(array('2' => 'unittest'), $p->getAllowedUsers(1));
        $this->assertTrue($p->isUserAllowed(1, 1)); // has admin priviledges
        $this->assertTrue($p->isUserAllowed(1, 2));
        
        // Check if revoked regular user is not allowed
        $this->assertTrue($p->allowUser(1, 1));
        $this->assertTrue($p->revokeUser(1, 2));
        $this->assertEquals(array('1' => 'admin'), $p->getAllowedUsers(1));
        $this->assertFalse($p->isUserAllowed(1, 2)); // regulat user is not allowed
    }
}
