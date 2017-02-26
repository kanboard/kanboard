<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\TaskHelper;

class TaskHelperTest extends Base
{
    public function testSelectPriority()
    {
        $helper = new TaskHelper($this->container);
        $this->assertNotEmpty($helper->renderPriorityField(array('priority_end' => '1', 'priority_start' => '5', 'priority_default' => '2'), array()));
        $this->assertNotEmpty($helper->renderPriorityField(array('priority_end' => '3', 'priority_start' => '1', 'priority_default' => '2'), array()));
    }

    public function testFormatPriority()
    {
        $helper = new TaskHelper($this->container);

        $this->assertEquals(
            '<span class="task-priority" title="Task priority">P2</span>',
            $helper->renderPriority(2)
        );

        $this->assertEquals(
            '<span class="task-priority" title="Task priority">-P6</span>',
            $helper->renderPriority(-6)
        );
    }
}
