<?php

namespace Kanboard\Event;

class TaskListEvent extends GenericEvent
{
    public function setTasks(array &$tasks)
    {
        $this->container['tasks'] =& $tasks;
    }
}
