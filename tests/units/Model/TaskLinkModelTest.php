<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskLinkModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;

class TaskLinkModelTest extends Base
{
    public function testGeyById()
    {
        $taskLinkModel = new TaskLinkModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'B')));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 6));

        $taskLink = $taskLinkModel->getById(1);
        $this->assertEquals(1, $taskLink['id']);
        $this->assertEquals(1, $taskLink['task_id']);
        $this->assertEquals(2, $taskLink['opposite_task_id']);
        $this->assertEquals(6, $taskLink['link_id']);
        $this->assertEquals(7, $taskLink['opposite_link_id']);
        $this->assertEquals('is a child of', $taskLink['label']);

        $taskLink = $taskLinkModel->getById(2);
        $this->assertEquals(2, $taskLink['id']);
        $this->assertEquals(2, $taskLink['task_id']);
        $this->assertEquals(1, $taskLink['opposite_task_id']);
        $this->assertEquals(7, $taskLink['link_id']);
        $this->assertEquals(6, $taskLink['opposite_link_id']);
        $this->assertEquals('is a parent of', $taskLink['label']);
    }

    // Check postgres issue: "Cardinality violation: 7 ERROR:  more than one row returned by a subquery used as an expression"
    public function testGetTaskWithMultipleMilestoneLink()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'B')));
        $this->assertEquals(3, $taskCreationModel->create(array('project_id' => 1, 'title' => 'C')));

        $this->assertNotFalse($taskLinkModel->create(1, 2, 9));
        $this->assertNotFalse($taskLinkModel->create(1, 3, 9));

        $task = $taskFinderModel->getExtendedQuery()->findOne();
        $this->assertNotEmpty($task);
    }

    public function testCreateTaskLinkWithNoOpposite()
    {
        $taskLinkModel = new TaskLinkModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'B')));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 1));

        $links = $taskLinkModel->getAll(1);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('relates to', $links[0]['label']);
        $this->assertEquals('B', $links[0]['title']);
        $this->assertEquals(2, $links[0]['task_id']);
        $this->assertEquals(1, $links[0]['is_active']);

        $links = $taskLinkModel->getAll(2);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('relates to', $links[0]['label']);
        $this->assertEquals('A', $links[0]['title']);
        $this->assertEquals(1, $links[0]['task_id']);
        $this->assertEquals(1, $links[0]['is_active']);

        $task_link = $taskLinkModel->getById(1);
        $this->assertNotEmpty($task_link);
        $this->assertEquals(1, $task_link['id']);
        $this->assertEquals(1, $task_link['task_id']);
        $this->assertEquals(2, $task_link['opposite_task_id']);
        $this->assertEquals(1, $task_link['link_id']);

        $opposite_task_link = $taskLinkModel->getOppositeTaskLink($task_link);
        $this->assertNotEmpty($opposite_task_link);
        $this->assertEquals(2, $opposite_task_link['id']);
        $this->assertEquals(2, $opposite_task_link['task_id']);
        $this->assertEquals(1, $opposite_task_link['opposite_task_id']);
        $this->assertEquals(1, $opposite_task_link['link_id']);
    }

    public function testCreateTaskLinkWithOpposite()
    {
        $taskLinkModel = new TaskLinkModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'B')));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 2));

        $links = $taskLinkModel->getAll(1);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('blocks', $links[0]['label']);
        $this->assertEquals('B', $links[0]['title']);
        $this->assertEquals(2, $links[0]['task_id']);
        $this->assertEquals(1, $links[0]['is_active']);

        $links = $taskLinkModel->getAll(2);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('is blocked by', $links[0]['label']);
        $this->assertEquals('A', $links[0]['title']);
        $this->assertEquals(1, $links[0]['task_id']);
        $this->assertEquals(1, $links[0]['is_active']);

        $task_link = $taskLinkModel->getById(1);
        $this->assertNotEmpty($task_link);
        $this->assertEquals(1, $task_link['id']);
        $this->assertEquals(1, $task_link['task_id']);
        $this->assertEquals(2, $task_link['opposite_task_id']);
        $this->assertEquals(2, $task_link['link_id']);

        $opposite_task_link = $taskLinkModel->getOppositeTaskLink($task_link);
        $this->assertNotEmpty($opposite_task_link);
        $this->assertEquals(2, $opposite_task_link['id']);
        $this->assertEquals(2, $opposite_task_link['task_id']);
        $this->assertEquals(1, $opposite_task_link['opposite_task_id']);
        $this->assertEquals(3, $opposite_task_link['link_id']);
    }

    public function testGroupByLabel()
    {
        $taskLinkModel = new TaskLinkModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));

        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'B')));
        $this->assertEquals(3, $taskCreationModel->create(array('project_id' => 1, 'title' => 'C')));

        $this->assertNotFalse($taskLinkModel->create(1, 2, 2));
        $this->assertNotFalse($taskLinkModel->create(1, 3, 2));

        $links = $taskLinkModel->getAllGroupedByLabel(1);
        $this->assertCount(1, $links);
        $this->assertArrayHasKey('blocks', $links);
        $this->assertCount(2, $links['blocks']);
        $this->assertEquals('test', $links['blocks'][0]['project_name']);
        $this->assertEquals('Backlog', $links['blocks'][0]['column_title']);
        $this->assertEquals('blocks', $links['blocks'][0]['label']);
    }

    public function testUpdate()
    {
        $taskLinkModel = new TaskLinkModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'test2')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 2, 'title' => 'B')));
        $this->assertEquals(3, $taskCreationModel->create(array('project_id' => 1, 'title' => 'C')));

        $this->assertEquals(1, $taskLinkModel->create(1, 2, 5));
        $this->assertTrue($taskLinkModel->update(1, 1, 3, 11));

        $links = $taskLinkModel->getAll(1);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('is fixed by', $links[0]['label']);
        $this->assertEquals('C', $links[0]['title']);
        $this->assertEquals(3, $links[0]['task_id']);

        $links = $taskLinkModel->getAll(2);
        $this->assertEmpty($links);

        $links = $taskLinkModel->getAll(3);
        $this->assertNotEmpty($links);
        $this->assertCount(1, $links);
        $this->assertEquals('fixes', $links[0]['label']);
        $this->assertEquals('A', $links[0]['title']);
        $this->assertEquals(1, $links[0]['task_id']);
    }

    public function testRemove()
    {
        $taskLinkModel = new TaskLinkModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'B')));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 2));

        $links = $taskLinkModel->getAll(1);
        $this->assertNotEmpty($links);
        $links = $taskLinkModel->getAll(2);
        $this->assertNotEmpty($links);

        $this->assertTrue($taskLinkModel->remove($links[0]['id']));

        $links = $taskLinkModel->getAll(1);
        $this->assertEmpty($links);
        $links = $taskLinkModel->getAll(2);
        $this->assertEmpty($links);
    }

    public function testGetProjectId()
    {
        $taskLinkModel = new TaskLinkModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'A')));
        $this->assertEquals(2, $taskCreationModel->create(array('project_id' => 1, 'title' => 'B')));
        $this->assertEquals(1, $taskLinkModel->create(1, 2, 2));

        $this->assertEquals(1, $taskLinkModel->getProjectId(1));
        $this->assertEquals(0, $taskLinkModel->getProjectId(42));
    }
}
