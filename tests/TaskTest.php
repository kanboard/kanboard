<?php

require_once __DIR__.'/../models/base.php';
require_once __DIR__.'/../models/task.php';

use Model\Task;

class TaskTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        defined('DB_FILENAME') or define('DB_FILENAME', ':memory:');
    }

    public function testDateFormat()
    {
        $t = new Task;

        $this->assertEquals('2014-03-05', date('Y-m-d', $t->getTimestampFromDate('05/03/2014', 'd/m/Y')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $t->getTimestampFromDate('03/05/2014', 'm/d/Y')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $t->getTimestampFromDate('3/5/2014', 'm/d/Y')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $t->getTimestampFromDate('5/3/2014', 'd/m/Y')));
        $this->assertEquals('2014-03-05', date('Y-m-d', $t->getTimestampFromDate('5/3/14', 'd/m/y')));
        $this->assertEquals(0, $t->getTimestampFromDate('5/3/14', 'd/m/Y'));
        $this->assertEquals(0, $t->getTimestampFromDate('5-3-2014', 'd/m/Y'));
    }
}
