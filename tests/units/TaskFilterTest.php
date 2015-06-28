<?php

require_once __DIR__.'/Base.php';

use Model\TaskFilter;

class TaskFilterTest extends Base
{
    public function testCopy()
    {
        $tf = new TaskFilter($this->container);
        $filter1 = $tf->create();
        $filter2 = $tf->copy();

        $this->assertTrue($filter1 !== $filter2);
        $this->assertTrue($filter1->query !== $filter2->query);
        $this->assertTrue($filter1->query->condition !== $filter2->query->condition);
    }
}
