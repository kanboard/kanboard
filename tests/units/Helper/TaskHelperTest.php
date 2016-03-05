<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\TaskHelper;

class TaskHelperTest extends Base
{
    public function testSelectPriority()
    {
        $helper = new TaskHelper($this->container);
        $this->assertNotEmpty($helper->selectPriority(array('priority_end' => '3', 'priority_start' => '1', 'priority_default' => '2'), array()));
        $this->assertEmpty($helper->selectPriority(array('priority_end' => '3', 'priority_start' => '3', 'priority_default' => '2'), array()));
    }

    public function testFormatPriority()
    {
        $helper = new TaskHelper($this->container);

        $this->assertEquals(
            '<span class="task-board-priority" title="Task priority">P2</span>',
            $helper->formatPriority(array('priority_end' => '3', 'priority_start' => '1', 'priority_default' => '2'), array('priority' => 2))
        );

        $this->assertEquals(
            '<span class="task-board-priority" title="Task priority">-P6</span>',
            $helper->formatPriority(array('priority_end' => '3', 'priority_start' => '1', 'priority_default' => '2'), array('priority' => -6))
        );

        $this->assertEmpty($helper->formatPriority(array('priority_end' => '3', 'priority_start' => '3', 'priority_default' => '2'), array()));
    }
}
