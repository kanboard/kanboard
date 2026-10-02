<?php

namespace KanboardTests\units\Controller;

use Kanboard\Controller\ICalendarController;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
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

    private function createFixtures()
    {
        $this->assertEquals(2, $this->container['userModel']->create(array('username' => 'user1')));
        $this->assertTrue($this->container['userModel']->enablePublicAccess(2));

        $this->assertEquals(1, $this->container['projectModel']->create(array('name' => 'Public project')));
        $this->assertTrue($this->container['projectModel']->enablePublicAccess(1));
        $this->assertEquals(2, $this->container['projectModel']->create(array('name' => 'Private project')));

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
