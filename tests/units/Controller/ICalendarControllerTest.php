<?php

namespace KanboardTests\units\Controller;

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
        $this->assertStringContainsString("BEGIN:VTODO\r\nUID:task-#3-todo\r\nSTATUS:NEEDS-ACTION\r\n", $this->calendar);
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

    private function renderCalendar(array $params)
    {
        $this->container['request'] = new Request($this->container, array('REQUEST_METHOD' => 'GET'), $params);

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
