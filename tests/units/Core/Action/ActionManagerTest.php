<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Action\ActionManager;
use Kanboard\Action\TaskAssignColorColumn;
use Kanboard\Action\TaskClose;
use Kanboard\Action\TaskCloseColumn;
use Kanboard\Action\TaskUpdateStartDate;
use Kanboard\Model\ActionModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Core\Security\Role;

class ActionManagerTest extends Base
{
    public function testRegister()
    {
        $actionManager = new ActionManager($this->container);
        $actionTaskClose = new TaskClose($this->container);

        $actionManager->register($actionTaskClose);
        $this->assertInstanceOf(get_class($actionTaskClose), $actionManager->getAction($actionTaskClose->getName()));
    }

    public function testGetActionNotFound()
    {
        $this->expectException('RuntimeException', 'Automatic Action Not Found: foobar');
        $actionManager = new ActionManager($this->container);
        $actionManager->getAction('foobar');
    }

    public function testGetAvailableActions()
    {
        $actionManager = new ActionManager($this->container);
        $actionTaskClose1 = new TaskCloseColumn($this->container);
        $actionTaskClose2 = new TaskCloseColumn($this->container);
        $actionTaskUpdateStartDate = new TaskUpdateStartDate($this->container);

        $actionManager
            ->register($actionTaskClose1)
            ->register($actionTaskClose2)
            ->register($actionTaskUpdateStartDate);

        $actions = $actionManager->getAvailableActions();
        $this->assertCount(2, $actions);
        $this->assertArrayHasKey($actionTaskClose1->getName(), $actions);
        $this->assertArrayHasKey($actionTaskUpdateStartDate->getName(), $actions);
        $this->assertNotEmpty($actions[$actionTaskClose1->getName()]);
        $this->assertNotEmpty($actions[$actionTaskUpdateStartDate->getName()]);
    }

    public function testGetAvailableParameters()
    {
        $actionManager = new ActionManager($this->container);

        $actionManager
            ->register(new TaskCloseColumn($this->container))
            ->register(new TaskUpdateStartDate($this->container));

        $params = $actionManager->getAvailableParameters(array(
            array('action_name' => '\Kanboard\Action\TaskCloseColumn'),
            array('action_name' => '\Kanboard\Action\TaskUpdateStartDate'),
        ));

        $this->assertCount(2, $params);
        $this->assertArrayHasKey('column_id', $params['\Kanboard\Action\TaskCloseColumn']);
        $this->assertArrayHasKey('column_id', $params['\Kanboard\Action\TaskUpdateStartDate']);
        $this->assertNotEmpty($params['\Kanboard\Action\TaskCloseColumn']['column_id']);
        $this->assertNotEmpty($params['\Kanboard\Action\TaskUpdateStartDate']['column_id']);
    }

    public function testGetCompatibleEvents()
    {
        $actionTaskAssignColorColumn = new TaskAssignColorColumn($this->container);
        $actionManager = new ActionManager($this->container);
        $actionManager->register($actionTaskAssignColorColumn);

        $events = $actionManager->getCompatibleEvents('\\'.get_class($actionTaskAssignColorColumn));
        $this->assertCount(2, $events);
        $this->assertArrayHasKey(TaskModel::EVENT_CREATE, $events);
        $this->assertArrayHasKey(TaskModel::EVENT_MOVE_COLUMN, $events);
        $this->assertNotEmpty($events[TaskModel::EVENT_CREATE]);
        $this->assertNotEmpty($events[TaskModel::EVENT_MOVE_COLUMN]);
    }

    public function testAttachEventsWithoutUserSession()
    {
        $projectModel = new ProjectModel($this->container);
        $actionModel = new ActionModel($this->container);
        $actionTaskAssignColorColumn = new TaskAssignColorColumn($this->container);
        $actionManager = new ActionManager($this->container);
        $actionManager->register($actionTaskAssignColorColumn);

        $actions = $actionManager->getAvailableActions();

        $actionManager->attachEvents();
        $this->assertEmpty($this->dispatcher->getListeners());

        $this->assertEquals(1, $projectModel->create(array('name' =>'test')));
        $this->assertEquals(1, $actionModel->create(array(
            'project_id' => 1,
            'event_name' => TaskModel::EVENT_CREATE,
            'action_name' => key($actions),
            'params' => array('column_id' => 1, 'color_id' => 'red'),
        )));

        $actionManager->attachEvents();
        $listeners = $this->dispatcher->getListeners(TaskModel::EVENT_CREATE);
        $this->assertCount(1, $listeners);
        $this->assertInstanceOf(get_class($actionTaskAssignColorColumn), $listeners[0][0]);

        $this->assertEquals(1, $listeners[0][0]->getProjectId());
    }

    public function testAttachEventsWithLoggedUser()
    {
        $_SESSION['user'] = array('id' => 1);

        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $actionModel = new ActionModel($this->container);
        $actionTaskAssignColorColumn = new TaskAssignColorColumn($this->container);
        $actionManager = new ActionManager($this->container);
        $actionManager->register($actionTaskAssignColorColumn);

        $actions = $actionManager->getAvailableActions();

        $this->assertEquals(1, $projectModel->create(array('name' =>'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' =>'test2')));

        $this->assertTrue($projectUserRoleModel->addUser(2, 1, Role::PROJECT_MEMBER));

        $this->assertEquals(1, $actionModel->create(array(
            'project_id' => 1,
            'event_name' => TaskModel::EVENT_CREATE,
            'action_name' => key($actions),
            'params' => array('column_id' => 1, 'color_id' => 'red'),
        )));

        $this->assertEquals(2, $actionModel->create(array(
            'project_id' => 2,
            'event_name' => TaskModel::EVENT_MOVE_COLUMN,
            'action_name' => key($actions),
            'params' => array('column_id' => 1, 'color_id' => 'red'),
        )));

        $actionManager->attachEvents();

        $listeners = $this->dispatcher->getListeners(TaskModel::EVENT_MOVE_COLUMN);
        $this->assertCount(1, $listeners);
        $this->assertInstanceOf(get_class($actionTaskAssignColorColumn), $listeners[0][0]);

        $this->assertEquals(2, $listeners[0][0]->getProjectId());
    }

    public function testThatEachListenerAreDifferentInstance()
    {
        $projectModel = new ProjectModel($this->container);
        $actionModel = new ActionModel($this->container);
        $actionTaskAssignColorColumn = new TaskAssignColorColumn($this->container);
        $actionManager = new ActionManager($this->container);
        $actionManager->register($actionTaskAssignColorColumn);

        $this->assertEquals(1, $projectModel->create(array('name' =>'test1')));
        $actions = $actionManager->getAvailableActions();

        $this->assertEquals(1, $actionModel->create(array(
            'project_id' => 1,
            'event_name' => TaskModel::EVENT_MOVE_COLUMN,
            'action_name' => key($actions),
            'params' => array('column_id' => 2, 'color_id' => 'green'),
        )));

        $this->assertEquals(2, $actionModel->create(array(
            'project_id' => 1,
            'event_name' => TaskModel::EVENT_MOVE_COLUMN,
            'action_name' => key($actions),
            'params' => array('column_id' => 1, 'color_id' => 'red'),
        )));

        $actionManager->attachEvents();

        $listeners = $this->dispatcher->getListeners(TaskModel::EVENT_MOVE_COLUMN);
        $this->assertCount(2, $listeners);
        $this->assertFalse($listeners[0][0] === $listeners[1][0]);

        $this->assertEquals(2, $listeners[0][0]->getParam('column_id'));
        $this->assertEquals('green', $listeners[0][0]->getParam('color_id'));

        $this->assertEquals(1, $listeners[1][0]->getParam('column_id'));
        $this->assertEquals('red', $listeners[1][0]->getParam('color_id'));
    }

    public function testRemoveEvents()
    {
        $projectModel = new ProjectModel($this->container);
        $actionModel = new ActionModel($this->container);
        $actionTaskAssignColorColumn = new TaskAssignColorColumn($this->container);
        $actionManager = new ActionManager($this->container);
        $actionManager->register($actionTaskAssignColorColumn);

        $actions = $actionManager->getAvailableActions();

        $this->assertEquals(1, $projectModel->create(array('name' =>'test')));
        $this->assertEquals(1, $actionModel->create(array(
            'project_id' => 1,
            'event_name' => TaskModel::EVENT_CREATE,
            'action_name' => key($actions),
            'params' => array('column_id' => 1, 'color_id' => 'red'),
        )));

        $actionManager->attachEvents();
        $this->dispatcher->addListener(TaskModel::EVENT_CREATE, function () {});

        $listeners = $this->dispatcher->getListeners(TaskModel::EVENT_CREATE);
        $this->assertCount(2, $listeners);

        $actionManager->removeEvents();

        $listeners = $this->dispatcher->getListeners(TaskModel::EVENT_CREATE);
        $this->assertCount(1, $listeners);
        $this->assertNotInstanceOf(get_class($actionTaskAssignColorColumn), $listeners[0]);
    }
}
