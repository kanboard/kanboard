<?php

namespace KanboardTests\units\Formatter;

use Eluceo\iCal\Component\Calendar;
use Kanboard\Formatter\TaskICalFormatter;
use KanboardTests\units\Base;

class TaskICalFormatterTest extends Base
{
    public function testAttendeeAndOrganizerDoNotExposeEmailAddresses()
    {
        $this->assertEquals(2, $this->container['userModel']->create(array('username' => 'creator', 'name' => 'Task Creator', 'email' => 'creator@example.org')));
        $this->assertEquals(3, $this->container['userModel']->create(array('username' => 'assignee', 'name' => 'Task Assignee', 'email' => 'assignee@example.org')));
        $this->assertEquals(1, $this->container['projectModel']->create(array('name' => 'P1')));
        $this->assertEquals(1, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Task',
            'creator_id' => 2,
            'owner_id' => 3,
            'date_due' => strtotime('+1 day'),
        )));

        $calendar = TaskICalFormatter::getInstance($this->container)
            ->setCalendar(new Calendar('Kanboard'))
            ->addTasksWithDueDateOnly($this->container['taskFinderModel']->getICalQuery())
            ->format();

        $this->assertStringContainsString('ATTENDEE;CN=Task Assignee:MAILTO:assignee@kanboard.local', $calendar);
        $this->assertStringContainsString('ORGANIZER;CN=Task Creator:MAILTO:creator@kanboard.local', $calendar);
        $this->assertStringNotContainsString('@example.org', $calendar);
    }
}
