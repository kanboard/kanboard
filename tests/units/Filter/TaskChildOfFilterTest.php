<?php

namespace KanboardTests\units\Filter;

use KanboardTests\units\Base;
use Kanboard\Filter\TaskChildOfFilter;
use Kanboard\Model\LinkModel;
use Kanboard\Model\TaskLinkModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\TaskFinderModel;

class TaskChildOfFilterTest extends Base
{
    private function createTasksWithChildren()
    {
        $projectModel = new ProjectModel($this->container);
        $taskCreation = new TaskCreationModel($this->container);
        $linkModel = new LinkModel($this->container);
        $taskLinkModel = new TaskLinkModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreation->create(array('title' => 'Parent task', 'project_id' => 1)));
        $this->assertEquals(2, $taskCreation->create(array('title' => 'Child A', 'project_id' => 1)));
        $this->assertEquals(3, $taskCreation->create(array('title' => 'Child B', 'project_id' => 1)));
        $this->assertEquals(4, $taskCreation->create(array('title' => 'Unrelated', 'project_id' => 1)));

        $childOfLink = $linkModel->getByLabel('is a child of');
        $this->assertNotEmpty($childOfLink);

        $this->assertNotFalse($taskLinkModel->create(2, 1, $childOfLink['id']));
        $this->assertNotFalse($taskLinkModel->create(3, 1, $childOfLink['id']));
    }

    private function applyFilter($value)
    {
        $taskFinder = new TaskFinderModel($this->container);
        $query = $taskFinder->getExtendedQuery();

        $filter = new TaskChildOfFilter();
        $filter->setDatabase($this->container['db']);
        $filter->withQuery($query);
        $filter->withValue($value);
        $filter->apply();

        $ids = array();
        foreach ($query->findAll() as $row) {
            $ids[] = $row['id'];
        }
        sort($ids);

        return $ids;
    }

    public function testChildOfById()
    {
        $this->createTasksWithChildren();
        $this->assertEquals(array(2, 3), $this->applyFilter('1'));
    }

    public function testChildOfByTitle()
    {
        $this->createTasksWithChildren();
        $this->assertEquals(array(2, 3), $this->applyFilter('Parent task'));
    }

    public function testChildOfByPartialTitleCaseInsensitive()
    {
        $this->createTasksWithChildren();
        $this->assertEquals(array(2, 3), $this->applyFilter('parent'));
    }

    public function testChildOfUnknownIdReturnsNothing()
    {
        $this->createTasksWithChildren();
        $this->assertEquals(array(), $this->applyFilter('999'));
    }

    public function testChildOfUnknownTitleReturnsNothing()
    {
        $this->createTasksWithChildren();
        $this->assertEquals(array(), $this->applyFilter('no such task'));
    }
}
