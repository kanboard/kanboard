<?php

require_once __DIR__.'/BaseProcedureTest.php';

class ActionProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project to test actions';

    public function testGetAvailableActions()
    {
        $actions = $this->app->getAvailableActions();
        $this->assertNotEmpty($actions);
        $this->assertInternalType('array', $actions);
        $this->assertArrayHasKey('\Kanboard\Action\TaskCloseColumn', $actions);
    }

    public function testGetAvailableActionEvents()
    {
        $events = $this->app->getAvailableActionEvents();
        $this->assertNotEmpty($events);
        $this->assertInternalType('array', $events);
        $this->assertArrayHasKey('task.move.column', $events);
    }

    public function testGetCompatibleActionEvents()
    {
        $events = $this->app->getCompatibleActionEvents('\Kanboard\Action\TaskCloseColumn');
        $this->assertNotEmpty($events);
        $this->assertInternalType('array', $events);
        $this->assertArrayHasKey('task.move.column', $events);
    }

    public function testCRUD()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateAction();
        $this->assertGetActions();
        $this->assertRemoveAction();
    }

    public function assertCreateAction()
    {
        $actionId = $this->app->createAction($this->projectId, 'task.move.column', '\Kanboard\Action\TaskCloseColumn', array('column_id' => 1));
        $this->assertNotFalse($actionId);
        $this->assertTrue($actionId > 0);
    }

    public function assertGetActions()
    {
        $actions = $this->app->getActions($this->projectId);
        $this->assertNotEmpty($actions);
        $this->assertInternalType('array', $actions);
        $this->assertArrayHasKey('id', $actions[0]);
        $this->assertArrayHasKey('project_id', $actions[0]);
        $this->assertArrayHasKey('event_name', $actions[0]);
        $this->assertArrayHasKey('action_name', $actions[0]);
        $this->assertArrayHasKey('params', $actions[0]);
        $this->assertArrayHasKey('column_id', $actions[0]['params']);
    }

    public function assertRemoveAction()
    {
        $actionId = $this->app->createAction($this->projectId, 'task.move.column', '\Kanboard\Action\TaskCloseColumn', array('column_id' => 1));
        $this->assertTrue($this->app->removeAction($actionId));
    }
}
