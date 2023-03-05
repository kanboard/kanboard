<?php

use Kanboard\Filter\TaskCategoryFilter;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\CategoryModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;
use Kanboard\Model\TaskModel;

require_once __DIR__.'/../Base.php';

class TaskCategoryFilterTest extends Base
{
    public function testFilterByCategoryName()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $categoryModel = new CategoryModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(['name' => 'Test']));
        $this->assertEquals(1, $categoryModel->create(['name' => 'Some category', 'project_id' => 1]));
        $this->assertEquals(1, $taskCreationModel->create(['project_id' => 1, 'title' => 'test1']));
        $this->assertEquals(2, $taskCreationModel->create(['project_id' => 1, 'title' => 'test2', 'category_id' => 1]));

        $filter = new TaskCategoryFilter();
        $filter->withQuery($query);
        $filter->withValue('Some category');
        $filter->apply();

        $tasks = $query->findAll();
        $this->assertCount(1, $tasks);
        $this->assertEquals('test2', $tasks[0]['title']);
    }

    public function testFilterByCategoryID()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $categoryModel = new CategoryModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(['name' => 'Test']));
        $this->assertEquals(1, $categoryModel->create(['name' => 'Some category', 'project_id' => 1]));
        $this->assertEquals(1, $taskCreationModel->create(['project_id' => 1, 'title' => 'test1']));
        $this->assertEquals(2, $taskCreationModel->create(['project_id' => 1, 'title' => 'test2', 'category_id' => 1]));

        $filter = new TaskCategoryFilter();
        $filter->withQuery($query);
        $filter->withValue('1');
        $filter->apply();

        $tasks = $query->findAll();
        $this->assertCount(1, $tasks);
        $this->assertEquals('test2', $tasks[0]['title']);
    }

    public function testFilterByNoCategory()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $categoryModel = new CategoryModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(['name' => 'Test']));
        $this->assertEquals(1, $categoryModel->create(['name' => 'Some category', 'project_id' => 1]));
        $this->assertEquals(1, $taskCreationModel->create(['project_id' => 1, 'title' => 'test1']));
        $this->assertEquals(2, $taskCreationModel->create(['project_id' => 1, 'title' => 'test2', 'category_id' => 1]));

        $filter = new TaskCategoryFilter();
        $filter->withQuery($query);
        $filter->withValue('none');
        $filter->apply();

        $tasks = $query->findAll();
        $this->assertCount(1, $tasks);
        $this->assertEquals('test1', $tasks[0]['title']);
    }

    public function testFilterByNumericCategoryName()
    {
        $taskFinderModel = new TaskFinderModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $categoryModel = new CategoryModel($this->container);
        $query = $taskFinderModel->getExtendedQuery();

        $this->assertEquals(1, $projectModel->create(['name' => 'Test']));
        $this->assertEquals(1, $categoryModel->create(['name' => '1234', 'project_id' => 1]));
        $this->assertEquals(1, $taskCreationModel->create(['project_id' => 1, 'title' => 'test1']));
        $this->assertEquals(2, $taskCreationModel->create(['project_id' => 1, 'title' => 'test2', 'category_id' => 1]));

        $filter = new TaskCategoryFilter();
        $filter->withQuery($query);
        $filter->withValue('1234');
        $filter->apply();

        $tasks = $query->findAll();
        $this->assertCount(1, $tasks);
        $this->assertEquals('test2', $tasks[0]['title']);
    }
}
