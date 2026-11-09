<?php

namespace KanboardTests\units\Model;

use KanboardTests\units\Base;
use Kanboard\Model\TaskModel;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;

class TaskModelTest extends Base
{
    public function testRemove()
    {
        $taskModel = new TaskModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Task #1', 'project_id' => 1)));

        $this->assertTrue($taskModel->remove(1));
        $this->assertFalse($taskModel->remove(1234));
    }

    public function testGetTaskIdFromText()
    {
        $taskModel = new TaskModel($this->container);
        $this->assertEquals(123, $taskModel->getTaskIdFromText('My task #123'));
        $this->assertEquals(0, $taskModel->getTaskIdFromText('My task 123'));
    }

    public function testHasValidProjectReferences()
    {
        $taskModel = new TaskModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $categoryModel = new CategoryModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Project #1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'Project #2')));
        $this->assertEquals(1, $categoryModel->create(array('name' => 'Category #1', 'project_id' => 1)));
        $this->assertEquals(2, $categoryModel->create(array('name' => 'Category #2', 'project_id' => 2)));

        $this->assertTrue($taskModel->hasValidProjectReferences(1, array()));
        $this->assertTrue($taskModel->hasValidProjectReferences(1, array('column_id' => 0, 'swimlane_id' => 0, 'category_id' => 0)));
        $this->assertTrue($taskModel->hasValidProjectReferences(1, array('column_id' => 4, 'swimlane_id' => 1, 'category_id' => 1)));
        $this->assertTrue($taskModel->hasValidProjectReferences('2', array('column_id' => '5', 'swimlane_id' => '2', 'category_id' => '2')));

        $this->assertFalse($taskModel->hasValidProjectReferences(1, array('column_id' => 5)));
        $this->assertFalse($taskModel->hasValidProjectReferences(1, array('swimlane_id' => 2)));
        $this->assertFalse($taskModel->hasValidProjectReferences(1, array('category_id' => 2)));
        $this->assertFalse($taskModel->hasValidProjectReferences(1, array('column_id' => 999)));
    }
}
