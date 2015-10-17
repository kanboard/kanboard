<?php

require_once __DIR__.'/../Base.php';

use Eluceo\iCal\Component\Calendar;
use Kanboard\Formatter\TaskFilterICalendarFormatter;
use Kanboard\Model\Project;
use Kanboard\Model\User;
use Kanboard\Model\TaskCreation;
use Kanboard\Core\DateParser;
use Kanboard\Model\Category;
use Kanboard\Model\Subtask;
use Kanboard\Model\Config;
use Kanboard\Model\Swimlane;

class TaskFilterICalendarFormatterTest extends Base
{
    public function testIcalEventsWithCreatorAndDueDate()
    {
        $dp = new DateParser($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilterICalendarFormatter($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task1', 'creator_id' => 1, 'date_due' => $dp->getTimestampFromIsoFormat('-2 days'))));

        $ics = $tf->create()
            ->filterByDueDateRange(strtotime('-1 month'), strtotime('+1 month'))
            ->setFullDay()
            ->setCalendar(new Calendar('Kanboard'))
            ->setColumns('date_due')
            ->addFullDayEvents()
            ->format();

        $this->assertContains('UID:task-#1-date_due', $ics);
        $this->assertContains('DTSTART;TZID=UTC;VALUE=DATE:'.date('Ymd', strtotime('-2 days')), $ics);
        $this->assertContains('DTEND;TZID=UTC;VALUE=DATE:'.date('Ymd', strtotime('-2 days')), $ics);
        $this->assertContains('URL:http://localhost/?controller=task&action=show&task_id=1&project_id=1', $ics);
        $this->assertContains('SUMMARY:#1 task1', $ics);
        $this->assertContains('ATTENDEE:MAILTO:admin@kanboard.local', $ics);
        $this->assertContains('X-MICROSOFT-CDO-ALLDAYEVENT:TRUE', $ics);
    }

    public function testIcalEventsWithAssigneeAndDueDate()
    {
        $dp = new DateParser($this->container);
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);
        $tf = new TaskFilterICalendarFormatter($this->container);
        $u = new User($this->container);
        $c = new Config($this->container);

        $this->assertNotFalse($c->save(array('application_url' => 'http://kb/')));
        $this->assertEquals('http://kb/', $c->get('application_url'));

        $this->assertNotFalse($u->update(array('id' => 1, 'email' => 'bob@localhost')));
        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertNotFalse($tc->create(array('project_id' => 1, 'title' => 'task1', 'owner_id' => 1, 'date_due' => $dp->getTimestampFromIsoFormat('+5 days'))));

        $ics = $tf->create()
            ->filterByDueDateRange(strtotime('-1 month'), strtotime('+1 month'))
            ->setFullDay()
            ->setCalendar(new Calendar('Kanboard'))
            ->setColumns('date_due')
            ->addFullDayEvents()
            ->format();

        $this->assertContains('UID:task-#1-date_due', $ics);
        $this->assertContains('DTSTART;TZID=UTC;VALUE=DATE:'.date('Ymd', strtotime('+5 days')), $ics);
        $this->assertContains('DTEND;TZID=UTC;VALUE=DATE:'.date('Ymd', strtotime('+5 days')), $ics);
        $this->assertContains('URL:http://kb/?controller=task&action=show&task_id=1&project_id=1', $ics);
        $this->assertContains('SUMMARY:#1 task1', $ics);
        $this->assertContains('ORGANIZER;CN=admin:MAILTO:bob@localhost', $ics);
        $this->assertContains('X-MICROSOFT-CDO-ALLDAYEVENT:TRUE', $ics);
    }
}
