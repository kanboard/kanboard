<?php

namespace KanboardTests\units\Import;

use Kanboard\Core\Security\Role;
use Kanboard\Import\TaskImport;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\ProjectUserRoleModel;
use Kanboard\Model\UserModel;
use KanboardTests\units\Base;

class TaskImportTest extends Base
{
    public function testImportOnlyAssignsProjectMembers()
    {
        $projectModel = new ProjectModel($this->container);
        $projectUserRoleModel = new ProjectUserRoleModel($this->container);
        $userModel = new UserModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'manager')));
        $this->assertEquals(3, $userModel->create(array('username' => 'member')));
        $this->assertEquals(4, $userModel->create(array('username' => 'viewer')));
        $this->assertEquals(5, $userModel->create(array('username' => 'outsider')));

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project A')));
        $this->assertTrue($projectUserRoleModel->addUser(1, 2, Role::PROJECT_MANAGER));
        $this->assertTrue($projectUserRoleModel->addUser(1, 3, Role::PROJECT_MEMBER));
        $this->assertTrue($projectUserRoleModel->addUser(1, 4, Role::PROJECT_VIEWER));

        $_SESSION['user'] = array(
            'id' => 2,
            'role' => Role::APP_USER,
            'username' => 'manager',
        );

        $taskImport = new TaskImport($this->container);
        $taskImport->setProjectId(1);

        $taskImport->importTask($this->buildRow('Task 1', 'member', 'member'), 1);
        $taskImport->importTask($this->buildRow('Task 2', 'outsider', 'outsider'), 2);
        $taskImport->importTask($this->buildRow('Task 3', 'viewer', 'viewer'), 3);
        $taskImport->importTask($this->buildRow('Task 4', 'unknown', 'unknown'), 4);

        $this->assertEquals(4, $taskImport->getNumberOfImportedTasks());

        $expected = array(
            1 => array('owner_id' => 3, 'creator_id' => 3),
            2 => array('owner_id' => 0, 'creator_id' => 2),
            3 => array('owner_id' => 0, 'creator_id' => 2),
            4 => array('owner_id' => 0, 'creator_id' => 2),
        );

        foreach ($expected as $taskId => $values) {
            $task = $this->container['taskFinderModel']->getById($taskId);
            $this->assertEquals(1, $task['project_id']);
            $this->assertEquals($values['owner_id'], $task['owner_id'], 'owner_id of task #'.$taskId);
            $this->assertEquals($values['creator_id'], $task['creator_id'], 'creator_id of task #'.$taskId);
        }
    }

    private function buildRow($title, $assignee, $creator)
    {
        $taskImport = new TaskImport($this->container);
        $row = array_fill_keys(array_keys($taskImport->getColumnMapping()), '');
        $row['title'] = $title;
        $row['assignee'] = $assignee;
        $row['creator'] = $creator;

        return $row;
    }
}
