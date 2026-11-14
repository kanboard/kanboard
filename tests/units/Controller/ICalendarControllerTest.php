<?php

namespace KanboardTests\units\Controller;

use DateTimeImmutable;
use DateTimeZone;
use Kanboard\Controller\ICalendarController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use KanboardTests\units\Base;

class ICalendarControllerTest extends Base
{
    private $calendar = '';

    public function testProjectCalendarIgnoresUnknownStartColumn()
    {
        $this->container['configModel']->save(array('calendar_project_tasks' => '1 OR 1=1 OR 1'));
        $this->createFixtures();
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token']))->project();

        $this->assertStringNotContainsString('Private task', $this->calendar);
        $this->assertStringContainsString('Public task', $this->calendar);
        $this->assertStringContainsString('task-#1-date_started-date_due', $this->calendar);
    }

    public function testUserCalendarIgnoresUnknownStartColumn()
    {
        $this->container['configModel']->save(array('calendar_user_tasks' => '1 OR 1=1 OR 1'));
        $this->createFixtures();
        $user = $this->container['userModel']->getById(2);

        $this->renderCalendar(array('token' => $user['token']))->user();

        $this->assertStringNotContainsString('Private task', $this->calendar);
        $this->assertStringContainsString('Public task', $this->calendar);
    }

    public function testProjectCalendarUsesCreationDateWhenConfigured()
    {
        $this->container['configModel']->save(array('calendar_project_tasks' => 'date_creation'));
        $this->createFixtures();
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token']))->project();

        $this->assertStringContainsString('task-#1-date_creation-date_due', $this->calendar);
    }

    public function testProjectCalendarDoesNotDuplicateDueDateTasksWhenCreationDateIsConfigured()
    {
        $this->container['configModel']->save(array('calendar_project_tasks' => 'date_creation'));
        $this->createFixtures();
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Due only',
            'date_due' => strtotime('+1 day'),
        )));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token']))->project();

        $this->assertSame(2, substr_count($this->calendar, 'BEGIN:VEVENT'));
        $this->assertStringContainsString('task-#3-date_creation-date_due', $this->calendar);
        $this->assertStringNotContainsString('task-#3-date_due', $this->calendar);
    }

    public function testProjectAndUserCalendarsDoNotExposeUserEmailAddresses()
    {
        $this->createFixtures();
        $this->assertTrue($this->container['userModel']->update(array('id' => 1, 'email' => 'admin@example.org')));
        $this->assertTrue($this->container['userModel']->update(array('id' => 2, 'email' => 'user1@example.org')));
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Created by admin',
            'owner_id' => 2,
            'creator_id' => 1,
            'date_due' => strtotime('+1 day'),
        )));
        $project = $this->container['projectModel']->getById(1);
        $user = $this->container['userModel']->getById(2);

        $this->renderCalendar(array('token' => $project['token']))->project();

        $this->assertStringNotContainsString('ATTENDEE', $this->calendar);
        $this->assertStringNotContainsString('ORGANIZER', $this->calendar);
        $this->assertStringNotContainsString('admin@example.org', $this->calendar);
        $this->assertStringNotContainsString('user1@example.org', $this->calendar);

        $this->renderCalendar(array('token' => $user['token']))->user();

        $this->assertStringNotContainsString('ATTENDEE', $this->calendar);
        $this->assertStringNotContainsString('ORGANIZER', $this->calendar);
        $this->assertStringNotContainsString('admin@example.org', $this->calendar);
        $this->assertStringNotContainsString('user1@example.org', $this->calendar);
    }

    public function testUserCalendarExcludesTasksFromRevokedProjects()
    {
        $this->createFixtures();
        $user = $this->container['userModel']->getById(2);

        $this->renderCalendar(array('token' => $user['token']))->user();
        $this->assertStringContainsString('Public task', $this->calendar);

        $this->assertTrue($this->container['projectUserRoleModel']->removeUser(1, 2));

        $this->renderCalendar(array('token' => $user['token']))->user();
        $this->assertStringNotContainsString('Public task', $this->calendar);
    }

    public function testProjectCalendarHasNoTodoByDefault()
    {
        $this->createFixtures();
        $this->assertEquals(3, $this->createTaskWithoutDates(1, 'Undated task', 2));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token']))->project();

        $this->assertStringNotContainsString('VTODO', $this->calendar);
        $this->assertStringNotContainsString('Undated task', $this->calendar);
        $this->assertStringContainsString('Public task', $this->calendar);
    }

    public function testProjectCalendarAddsTasksWithoutDatesAsTodoOnRequest()
    {
        $this->createFixtures();
        $this->assertEquals(3, $this->createTaskWithoutDates(1, 'Undated task', 2));
        $this->assertEquals(4, $this->createTaskWithoutDates(2, 'Private undated task', 2));
        $this->assertEquals(5, $this->createTaskWithoutDates(1, 'Closed undated task', 2));
        $this->assertTrue($this->container['taskStatusModel']->close(5));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token'], 'todo' => '1'))->project();

        $this->assertSame(1, substr_count($this->calendar, 'BEGIN:VTODO'));
        $this->assertStringContainsString("BEGIN:VTODO\r\nUID:task-#3-todo@localhost\r\nSTATUS:NEEDS-ACTION\r\n", $this->calendar);
        $this->assertStringContainsString("SUMMARY:#3 Undated task\r\n", $this->calendar);
        $this->assertStringNotContainsString('ATTENDEE', $this->calendar);
        $this->assertStringNotContainsString('ORGANIZER', $this->calendar);
        $this->assertStringNotContainsString('Private undated task', $this->calendar);
        $this->assertStringNotContainsString('Closed undated task', $this->calendar);

        $this->assertSame(1, substr_count($this->calendar, 'BEGIN:VEVENT'));
        $this->assertStringContainsString('task-#1-date_started-date_due', $this->calendar);
        $this->assertStringNotContainsString('task-#1-todo', $this->calendar);
    }

    public function testProjectCalendarKeepsTasksWithOneDateOutOfTodos()
    {
        $this->createFixtures();
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Due only',
            'date_due' => strtotime('+1 day'),
        )));
        $this->assertEquals(4, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Started only',
            'date_started' => strtotime('-1 day'),
        )));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token'], 'todo' => '1'))->project();

        $this->assertStringNotContainsString('VTODO', $this->calendar);
        $this->assertStringContainsString('task-#3-date_due', $this->calendar);
        $this->assertStringContainsString('task-#4-date_started-date_due', $this->calendar);
    }

    public function testProjectCalendarHasNoTodoWhenCreationDateIsTheStartDate()
    {
        $this->container['configModel']->save(array('calendar_project_tasks' => 'date_creation'));
        $this->createFixtures();
        $this->assertEquals(3, $this->createTaskWithoutDates(1, 'Undated task', 2));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token'], 'todo' => '1'))->project();

        $this->assertStringNotContainsString('VTODO', $this->calendar);
        $this->assertStringContainsString('task-#3-date_creation-date_due', $this->calendar);
    }

    public function testUserCalendarAddsAssignedTasksWithoutDatesAsTodoOnRequest()
    {
        $this->createFixtures();
        $this->assertEquals(3, $this->createTaskWithoutDates(1, 'My undated task', 2));
        $this->assertEquals(4, $this->createTaskWithoutDates(1, 'Someone else undated task', 1));
        $this->assertEquals(5, $this->createTaskWithoutDates(2, 'Private undated task', 2));
        $user = $this->container['userModel']->getById(2);

        $this->renderCalendar(array('token' => $user['token']))->user();
        $this->assertStringNotContainsString('VTODO', $this->calendar);

        $this->renderCalendar(array('token' => $user['token'], 'todo' => '1'))->user();

        $this->assertSame(1, substr_count($this->calendar, 'BEGIN:VTODO'));
        $this->assertStringContainsString('UID:task-#3-todo', $this->calendar);
        $this->assertStringContainsString('My undated task', $this->calendar);
        $this->assertStringNotContainsString('Someone else undated task', $this->calendar);
        $this->assertStringNotContainsString('Private undated task', $this->calendar);
        $this->assertStringContainsString('Public task', $this->calendar);

        $this->assertTrue($this->container['projectUserRoleModel']->removeUser(1, 2));

        $this->renderCalendar(array('token' => $user['token'], 'todo' => '1'))->user();
        $this->assertStringNotContainsString('VTODO', $this->calendar);
    }

    public function testProjectCalendarDoesNotEndEventsBeforeTheirStart()
    {
        $this->createFixtures();
        $futureStart = strtotime('+3 days');
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Starts later',
            'date_started' => $futureStart,
        )));
        $this->assertEquals(4, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Started after the due date',
            'date_due' => strtotime('-2 days'),
        )));
        $this->assertTrue($this->container['taskModificationModel']->update(array('id' => 4, 'date_started' => time())));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token']))->project();

        $event = $this->getComponent('task-#3-date_started-date_due');
        $this->assertStringContainsString('DTSTART:'.gmdate('Ymd\THis\Z', $futureStart)."\r\n", $event);
        $this->assertStringNotContainsString('DTEND', $event);

        $event = $this->getComponent('task-#4-date_started-date_due');
        $this->assertStringContainsString('DTSTART:', $event);
        $this->assertStringNotContainsString('DTEND', $event);

        $event = $this->getComponent('task-#1-date_started-date_due');
        $this->assertStringContainsString('DTSTART:', $event);
        $this->assertStringContainsString('DTEND:', $event);
    }

    public function testProjectCalendarEndsStartedTasksWithoutDueDateNow()
    {
        $this->createFixtures();
        $start = strtotime('-3 days');
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Started only',
            'date_started' => $start,
        )));
        $project = $this->container['projectModel']->getById(1);

        $before = time();
        $this->renderCalendar(array('token' => $project['token']))->project();

        $event = $this->getComponent('task-#3-date_started-date_due');
        $this->assertStringContainsString('DTSTART:'.gmdate('Ymd\THis\Z', $start)."\r\n", $event);
        $this->assertSame(1, preg_match('/^DTEND:(\d{8}T\d{6}Z)\r$/m', $event, $matches));
        $this->assertGreaterThanOrEqual(gmdate('Ymd\THis\Z', $before), $matches[1]);
    }

    public function testProjectCalendarHasNoEndForDueDateWithTime()
    {
        $this->createFixtures();
        $due = strtotime('tomorrow 14:30');
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Due with time',
            'date_due' => $due,
        )));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token']))->project();

        $event = $this->getComponent('task-#3-date_due');
        $this->assertStringContainsString('DTSTART:'.gmdate('Ymd\T', $due)."143000Z\r\n", $event);
        $this->assertStringNotContainsString('DTEND', $event);
    }

    public function testProjectCalendarWritesStartAndDueDatesWithoutTimeAsAllDayEvent()
    {
        $this->createFixtures();
        $day = strtotime('tomorrow');
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Same day',
            'date_started' => $day,
            'date_due' => $day,
        )));
        $this->assertEquals(4, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Several days',
            'date_started' => $day,
            'date_due' => strtotime('tomorrow +3 days'),
        )));
        $this->assertEquals(5, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Starts later without time',
            'date_started' => $day,
        )));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token']))->project();

        $event = $this->getComponent('task-#3-date_started-date_due');
        $this->assertStringContainsString('DTSTART;VALUE=DATE:'.date('Ymd', $day)."\r\n", $event);
        $this->assertStringContainsString('DTEND;VALUE=DATE:'.date('Ymd', strtotime('tomorrow +1 day'))."\r\n", $event);

        $event = $this->getComponent('task-#4-date_started-date_due');
        $this->assertStringContainsString('DTSTART;VALUE=DATE:'.date('Ymd', $day)."\r\n", $event);
        $this->assertStringContainsString('DTEND;VALUE=DATE:'.date('Ymd', strtotime('tomorrow +4 days'))."\r\n", $event);

        $event = $this->getComponent('task-#5-date_started-date_due');
        $this->assertStringContainsString('DTSTART;VALUE=DATE:'.date('Ymd', $day)."\r\n", $event);
        $this->assertStringNotContainsString('DTEND', $event);
    }

    public function testUserCalendarUsesTheUserTimezone()
    {
        $this->createFixtures();
        $this->assertTrue($this->container['userModel']->update(array('id' => 2, 'timezone' => 'Asia/Tokyo')));
        $due = new DateTimeImmutable('tomorrow', new DateTimeZone('Asia/Tokyo'));
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Due date without time',
            'owner_id' => 2,
            'date_due' => $due->getTimestamp(),
        )));
        $user = $this->container['userModel']->getById(2);

        // A calendar client has no session: the current timezone is the application one
        $this->renderCalendar(array('token' => $user['token']))->user();

        $event = $this->getComponent('task-#3-date_due');
        $this->assertStringContainsString('DTSTART;VALUE=DATE:'.$due->format('Ymd')."\r\n", $event);
        $this->assertStringContainsString('DTEND;VALUE=DATE:'.$due->modify('+1 day')->format('Ymd')."\r\n", $event);
    }

    public function testUserCalendarFallsBackToTheApplicationTimezone()
    {
        $this->container['configModel']->save(array('application_timezone' => 'America/New_York'));
        $this->createFixtures();
        $this->assertTrue($this->container['userModel']->update(array('id' => 2, 'timezone' => 'Not/AZone')));
        $due = new DateTimeImmutable('tomorrow', new DateTimeZone('America/New_York'));
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Due date without time',
            'owner_id' => 2,
            'date_due' => $due->getTimestamp(),
        )));
        $user = $this->container['userModel']->getById(2);

        $this->renderCalendar(array('token' => $user['token']))->user();
        $this->assertStringContainsString('DTSTART;VALUE=DATE:'.$due->format('Ymd')."\r\n", $this->getComponent('task-#3-date_due'));

        $this->assertTrue($this->container['userModel']->update(array('id' => 2, 'timezone' => '')));

        $this->renderCalendar(array('token' => $user['token']))->user();
        $this->assertStringContainsString('DTSTART;VALUE=DATE:'.$due->format('Ymd')."\r\n", $this->getComponent('task-#3-date_due'));
    }

    public function testProjectCalendarDoesNotDependOnTheCurrentTimezone()
    {
        $this->container['configModel']->save(array('application_timezone' => 'Europe/Paris'));
        $this->createFixtures();
        $due = new DateTimeImmutable('tomorrow', new DateTimeZone('Europe/Paris'));
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Due date without time',
            'date_due' => $due->getTimestamp(),
        )));
        $project = $this->container['projectModel']->getById(1);

        foreach (array('UTC', 'Asia/Tokyo', 'America/Los_Angeles') as $currentTimezone) {
            // The timezone of the session when the feed is opened by a logged user
            date_default_timezone_set($currentTimezone);

            $this->renderCalendar(array('token' => $project['token']))->project();

            $event = $this->getComponent('task-#3-date_due');
            $this->assertStringContainsString('DTSTART;VALUE=DATE:'.$due->format('Ymd')."\r\n", $event, $currentTimezone);
            $this->assertStringContainsString('DTEND;VALUE=DATE:'.$due->modify('+1 day')->format('Ymd')."\r\n", $event, $currentTimezone);
        }

        date_default_timezone_set('UTC');
    }

    public function testCalendarsLinkToTheTaskWithTheApplicationUrl()
    {
        $this->container['configModel']->save(array('application_url' => 'https://example.org/kanboard/'));
        $this->createFixtures();
        $project = $this->container['projectModel']->getById(1);
        $user = $this->container['userModel']->getById(2);
        $server = array('PHP_SELF' => '/kanboard/index.php');
        $url = "URL:https://example.org/kanboard/?controller=TaskViewController&action=show&task_id=1\r\n";

        $this->renderCalendar(array('token' => $project['token']), $server)->project();
        $this->assertStringContainsString($url, $this->getComponent('task-#1-date_started-date_due'));

        $this->renderCalendar(array('token' => $user['token']), $server)->user();
        $this->assertStringContainsString($url, $this->getComponent('task-#1-date_started-date_due'));
    }

    public function testProjectCalendarRendersDescriptionsLikeThePublicTaskView()
    {
        $this->createFixtures();
        $this->assertTrue($this->container['userModel']->update(array('id' => 2, 'name' => 'John Doe')));
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'With description',
            'description' => 'See #1 and ask @user1',
            'date_due' => strtotime('+1 day'),
        )));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token']))->project();

        $event = $this->getComponent('task-#3-date_due');
        $this->assertStringContainsString("DESCRIPTION:See #1 and ask @user1\r\n", $event);
        $this->assertStringContainsString('X-ALT-DESC;FMTTYPE=text/html:', $event);
        $this->assertStringContainsString('ask @user1</p>', $event);
        $this->assertStringNotContainsString('John Doe', $event);
        $this->assertStringNotContainsString('UserViewController', $event);
        $this->assertStringNotContainsString('user_id', $event);
        $this->assertStringNotContainsString('href="/', $event);
        $this->assertStringContainsString('<a href="http://localhost/?controller=TaskViewController&amp\;action=readonly&amp\;token='.$project['token'].'&amp\;task_id=1">#1</a>', $event);
    }

    public function testAdminUserCalendarExcludesTasksFromClosedProjects()
    {
        $this->createFixtures();
        $this->assertTrue($this->container['userModel']->enablePublicAccess(1));
        $this->assertEquals(3, $this->createTask(1, 'Admin task', 1));
        $admin = $this->container['userModel']->getById(1);

        $this->renderCalendar(array('token' => $admin['token']))->user();
        $this->assertStringContainsString('Admin task', $this->calendar);
        $this->assertStringContainsString('Private task', $this->calendar);
        $this->assertSame(2, substr_count($this->calendar, 'BEGIN:VEVENT'));

        $this->assertTrue($this->container['projectModel']->disable(1));

        $this->renderCalendar(array('token' => $admin['token']))->user();
        $this->assertStringNotContainsString('Admin task', $this->calendar);
        $this->assertStringContainsString('Private task', $this->calendar);
        $this->assertSame(1, substr_count($this->calendar, 'BEGIN:VEVENT'));
    }

    public function testProjectCalendarExcludesTasksWithoutDueDateStartedBeforeTheTimeRange()
    {
        $this->createFixtures();
        $this->assertEquals(3, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Started long ago',
            'date_started' => strtotime('-3 months'),
        )));
        $this->assertEquals(4, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Started long ago with due date',
            'date_started' => strtotime('-3 months'),
            'date_due' => strtotime('+1 week'),
        )));
        $this->assertEquals(5, $this->container['taskCreationModel']->create(array(
            'project_id' => 1,
            'title' => 'Started recently',
            'date_started' => strtotime('-1 month'),
        )));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token']))->project();

        $this->assertStringNotContainsString('task-#3-', $this->calendar);
        $this->assertStringContainsString('task-#4-date_started-date_due', $this->calendar);
        $this->assertStringContainsString('task-#5-date_started-date_due', $this->calendar);
        $this->assertSame(3, substr_count($this->calendar, 'BEGIN:VEVENT'));
    }

    public function testProjectCalendarExcludesOldTasksWithoutDueDateWhenCreationDateIsTheStartDate()
    {
        $this->container['configModel']->save(array('calendar_project_tasks' => 'date_creation'));
        $this->createFixtures();
        $this->assertEquals(3, $this->createTaskWithoutDates(1, 'Old backlog task', 2));
        $this->assertEquals(4, $this->createTaskWithoutDates(1, 'Recent backlog task', 2));
        $this->container['db']->table('tasks')->eq('id', 3)->update(array('date_creation' => strtotime('-1 year')));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token'], 'todo' => '1'))->project();

        $this->assertStringNotContainsString('Old backlog task', $this->calendar);
        $this->assertStringContainsString('task-#4-date_creation-date_due', $this->calendar);
    }

    public function testCalendarUidsContainTheApplicationHost()
    {
        $this->container['configModel']->save(array('application_url' => 'https://example.org/kanboard/'));
        $this->createFixtures();
        $this->assertEquals(3, $this->createTaskWithoutDates(1, 'Undated task', 2));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token'], 'todo' => '1'))->project();

        $this->assertStringContainsString("\r\nUID:task-#1-date_started-date_due@example.org\r\n", $this->calendar);
        $this->assertStringContainsString("\r\nUID:task-#3-todo@example.org\r\n", $this->calendar);
    }

    public function testCalendarStampIsTheTaskModificationDate()
    {
        $this->createFixtures();
        $modified = strtotime('-10 days');
        $this->container['db']->table('tasks')->eq('id', 1)->update(array('date_modification' => $modified));
        $project = $this->container['projectModel']->getById(1);

        $this->renderCalendar(array('token' => $project['token']))->project();

        $event = $this->getComponent('task-#1-date_started-date_due');
        $this->assertStringContainsString("\r\nDTSTAMP:".gmdate('Ymd\THis\Z', $modified)."\r\n", $event);
        $this->assertStringContainsString("\r\nLAST-MODIFIED:".gmdate('Ymd\THis\Z', $modified)."\r\n", $event);
    }

    /**
     * Get the unfolded lines of a component
     */
    private function getComponent($uid)
    {
        $pattern = '/BEGIN:(VEVENT|VTODO)\r\nUID:'.preg_quote($uid, '/').'@[^\r]+\r\n.*?END:\1\r\n/s';
        $this->assertSame(1, preg_match($pattern, $this->calendar, $matches), 'Component not found: '.$uid);

        return str_replace("\r\n ", '', $matches[0]);
    }

    private function createFixtures()
    {
        $this->assertEquals(2, $this->container['userModel']->create(array('username' => 'user1')));
        $this->assertTrue($this->container['userModel']->enablePublicAccess(2));

        $this->assertEquals(1, $this->container['projectModel']->create(array('name' => 'Public project')));
        $this->assertTrue($this->container['projectModel']->enablePublicAccess(1));
        $this->assertEquals(2, $this->container['projectModel']->create(array('name' => 'Private project')));
        $this->assertTrue($this->container['projectUserRoleModel']->addUser(1, 2, Role::PROJECT_MEMBER));

        $this->assertEquals(1, $this->createTask(1, 'Public task', 2));
        $this->assertEquals(2, $this->createTask(2, 'Private task', 1));
    }

    private function createTask($projectId, $title, $ownerId)
    {
        return $this->container['taskCreationModel']->create(array(
            'project_id' => $projectId,
            'title' => $title,
            'owner_id' => $ownerId,
            'date_started' => strtotime('-1 day'),
            'date_due' => strtotime('+1 day'),
        ));
    }

    private function createTaskWithoutDates($projectId, $title, $ownerId)
    {
        return $this->container['taskCreationModel']->create(array(
            'project_id' => $projectId,
            'title' => $title,
            'owner_id' => $ownerId,
        ));
    }

    private function renderCalendar(array $params, array $server = array())
    {
        $this->container['request'] = new Request($this->container, array('REQUEST_METHOD' => 'GET') + $server, $params);

        $this->container['response'] = $this->getMockBuilder(Response::class)
            ->setConstructorArgs(array($this->container))
            ->onlyMethods(array('ical'))
            ->getMock();

        $this->container['response']
            ->expects($this->once())
            ->method('ical')
            ->willReturnCallback(function ($calendar) {
                $this->calendar = $calendar;
            });

        return new ICalendarController($this->container);
    }
}
