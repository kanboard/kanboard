<?php

require_once __DIR__.'/../models/base.php';
require_once __DIR__.'/../models/acl.php';

use Model\Acl;

class AclTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        defined('DB_FILENAME') or define('DB_FILENAME', ':memory:');
    }

    public function testAllowedAction()
    {
        $acl_rules = array(
            'controller1' => array('action1', 'action3'),
        );

        $acl = new Acl;
        $this->assertTrue($acl->isAllowedAction($acl_rules, 'controller1', 'action1'));
        $this->assertTrue($acl->isAllowedAction($acl_rules, 'controller1', 'action3'));
        $this->assertFalse($acl->isAllowedAction($acl_rules, 'controller1', 'action2'));
        $this->assertFalse($acl->isAllowedAction($acl_rules, 'controller2', 'action2'));
        $this->assertFalse($acl->isAllowedAction($acl_rules, 'controller2', 'action3'));
    }

    public function testIsAdmin()
    {
        $acl = new Acl;

        $_SESSION = array();
        $this->assertFalse($acl->isAdminUser());

        $_SESSION = array('user' => array());
        $this->assertFalse($acl->isAdminUser());

        $_SESSION = array('user' => array('is_admin' => true));
        $this->assertFalse($acl->isAdminUser());

        $_SESSION = array('user' => array('is_admin' => '0'));
        $this->assertFalse($acl->isAdminUser());

        $_SESSION = array('user' => array('is_admin' => '2'));
        $this->assertFalse($acl->isAdminUser());

        $_SESSION = array('user' => array('is_admin' => '1'));
        $this->assertTrue($acl->isAdminUser());
    }

    public function testIsUser()
    {
        $acl = new Acl;

        $_SESSION = array();
        $this->assertFalse($acl->isRegularUser());

        $_SESSION = array('user' => array());
        $this->assertFalse($acl->isRegularUser());

        $_SESSION = array('user' => array('is_admin' => true));
        $this->assertFalse($acl->isRegularUser());

        $_SESSION = array('user' => array('is_admin' => '1'));
        $this->assertFalse($acl->isRegularUser());

        $_SESSION = array('user' => array('is_admin' => '2'));
        $this->assertFalse($acl->isRegularUser());

        $_SESSION = array('user' => array('is_admin' => '0'));
        $this->assertTrue($acl->isRegularUser());
    }

    public function testIsPageAllowed()
    {
        $acl = new Acl;

        // Public access
        $_SESSION = array();
        $this->assertFalse($acl->isPageAccessAllowed('user', 'create'));
        $this->assertFalse($acl->isPageAccessAllowed('user', 'save'));
        $this->assertFalse($acl->isPageAccessAllowed('user', 'remove'));
        $this->assertFalse($acl->isPageAccessAllowed('user', 'confirm'));
        $this->assertFalse($acl->isPageAccessAllowed('app', 'index'));
        $this->assertFalse($acl->isPageAccessAllowed('user', 'index'));
        $this->assertTrue($acl->isPageAccessAllowed('user', 'login'));
        $this->assertTrue($acl->isPageAccessAllowed('user', 'check'));
        $this->assertTrue($acl->isPageAccessAllowed('task', 'add'));
        $this->assertTrue($acl->isPageAccessAllowed('board', 'readonly'));

        // Regular user
        $_SESSION = array('user' => array('is_admin' => '0'));
        $this->assertFalse($acl->isPageAccessAllowed('user', 'create'));
        $this->assertFalse($acl->isPageAccessAllowed('user', 'save'));
        $this->assertFalse($acl->isPageAccessAllowed('user', 'remove'));
        $this->assertFalse($acl->isPageAccessAllowed('user', 'confirm'));
        $this->assertTrue($acl->isPageAccessAllowed('app', 'index'));
        $this->assertTrue($acl->isPageAccessAllowed('user', 'index'));
        $this->assertTrue($acl->isPageAccessAllowed('user', 'login'));
        $this->assertTrue($acl->isPageAccessAllowed('user', 'check'));
        $this->assertTrue($acl->isPageAccessAllowed('task', 'add'));
        $this->assertTrue($acl->isPageAccessAllowed('board', 'readonly'));

        // Admin user
        $_SESSION = array('user' => array('is_admin' => '1'));
        $this->assertTrue($acl->isPageAccessAllowed('user', 'create'));
        $this->assertTrue($acl->isPageAccessAllowed('user', 'save'));
        $this->assertTrue($acl->isPageAccessAllowed('user', 'remove'));
        $this->assertTrue($acl->isPageAccessAllowed('user', 'confirm'));
        $this->assertTrue($acl->isPageAccessAllowed('app', 'index'));
        $this->assertTrue($acl->isPageAccessAllowed('user', 'index'));
        $this->assertTrue($acl->isPageAccessAllowed('user', 'login'));
        $this->assertTrue($acl->isPageAccessAllowed('user', 'check'));
        $this->assertTrue($acl->isPageAccessAllowed('task', 'add'));
        $this->assertTrue($acl->isPageAccessAllowed('board', 'readonly'));
    }
}
