<?php

require_once __DIR__.'/../Base.php';

use Formatter\TaskFilterCalendarFormatter;
use Model\Project;
use Model\User;
use Model\TaskCreation;
use Model\DateParser;
use Model\Category;
use Model\Subtask;
use Model\Config;
use Model\Swimlane;

class TaskFilterCalendarFormatterTest extends Base
{
    public function testCopy()
    {
        $tf = new TaskFilterCalendarFormatter($this->container);
        $filter1 = $tf->create()->setFullDay();
        $filter2 = $tf->copy();

        $this->assertTrue($filter1 !== $filter2);
        $this->assertTrue($filter1->query !== $filter2->query);
        $this->assertTrue($filter1->query->condition !== $filter2->query->condition);
        $this->assertTrue($filter1->isFullDay());
        $this->assertFalse($filter2->isFullDay());
    }
}
