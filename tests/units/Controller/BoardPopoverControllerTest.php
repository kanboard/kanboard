<?php

namespace KanboardTests\units\Controller;

use DOMDocument;
use Kanboard\Controller\BoardPopoverController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Http\Response;
use Kanboard\Core\Security\Role;
use KanboardTests\units\Base;

class BoardPopoverControllerTest extends Base
{
    private $ids;
    private $column;
    private $lane;
    private $otherColumn;
    private $otherLane;

    private function createBoard($role = Role::PROJECT_MANAGER)
    {
        $this->container['userModel']->create(array('username' => 'board-user'));
        $this->container['projectModel']->create(array('name' => 'First'));
        $this->container['projectModel']->create(array('name' => 'Second'));
        $this->container['projectUserRoleModel']->addUser(1, 2, $role);
        $this->container['userSession']->initialize($this->container['userModel']->getById(2));
        $this->otherLane = $this->container['swimlaneModel']->create(1, 'Other lane');
        $create = $this->container['taskCreationModel'];
        $this->ids['visible'] = $create->create(array('project_id' => 1, 'title' => 'needle'));
        $task = $this->container['taskFinderModel']->getById($this->ids['visible']);
        $this->column = $task['column_id'];
        $this->lane = $task['swimlane_id'];
        $columns = $this->container['columnModel']->getAll(1);
        $this->otherColumn = $columns[1]['id'];
        $this->ids['hidden'] = $create->create(array('project_id' => 1, 'title' => 'hidden'));
        $this->ids['column'] = $create->create(array('project_id' => 1, 'title' => 'needle', 'column_id' => $this->otherColumn));
        $this->ids['lane'] = $create->create(array('project_id' => 1, 'title' => 'needle', 'swimlane_id' => $this->otherLane));
        $this->ids['project'] = $create->create(array('project_id' => 2, 'title' => 'needle'));
        $this->ids['closed'] = $create->create(array('project_id' => 1, 'title' => 'needle', 'is_active' => 0));
    }

    private function confirmation($filter)
    {
        $this->container['userSession']->setFilters(1, $filter);
        $this->container['request'] = new Request($this->container, array('REQUEST_METHOD' => 'GET'), array(
            'project_id' => 1, 'column_id' => $this->column, 'swimlane_id' => $this->lane,
        ));
        $html = '';
        $response = $this->getMockBuilder(Response::class)->setConstructorArgs(array($this->container))
            ->onlyMethods(array('html', 'redirect'))->getMock();
        $response->method('html')->willReturnCallback(function ($body) use (&$html) { $html = $body; });
        $this->container['response'] = $response;
        (new BoardPopoverController($this->container))->confirmCloseColumnTasks();
        return $html;
    }

    private function submitConfirmation($html, array $overrides = array())
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $values = array();
        foreach ($dom->getElementsByTagName('input') as $input) {
            if ($input->getAttribute('type') === 'hidden') {
                $values[$input->getAttribute('name')] = $input->getAttribute('value');
            }
        }
        $values = array_merge($values, $overrides);
        $this->container['request'] = new Request($this->container, array('REQUEST_METHOD' => 'POST'), array('project_id' => 1), $values);
        (new BoardPopoverController($this->container))->closeColumnTasks();
    }

    public function testFilteredConfirmationCountsOnlyMatchingOpenTasks()
    {
        $this->createBoard();
        $html = $this->confirmation('status:open needle');
        $this->assertStringContainsString('1 task(s) in the column', $html);
    }

    public function testFilteredConfirmationClosesOnlyMatchingTasksInSelectedCell()
    {
        $this->createBoard();
        $html = $this->confirmation('status:open needle');
        $this->submitConfirmation($html);
        $status = $this->container['taskStatusModel'];
        $this->assertTrue($status->isClosed($this->ids['visible']));
        foreach (array('hidden', 'column', 'lane', 'project') as $key) {
            $this->assertTrue($status->isOpen($this->ids[$key]), $key);
        }
        $this->assertTrue($status->isClosed($this->ids['closed']));
    }

    public function testDefaultFilterCountsAndClosesAllOpenTasksInSelectedCell()
    {
        $this->createBoard();
        $html = $this->confirmation('status:open');
        $this->assertStringContainsString('2 task(s) in the column', $html);
        $this->submitConfirmation($html);
        $status = $this->container['taskStatusModel'];
        foreach (array('visible', 'hidden', 'closed') as $key) {
            $this->assertTrue($status->isClosed($this->ids[$key]), $key);
        }
        foreach (array('column', 'lane', 'project') as $key) {
            $this->assertTrue($status->isOpen($this->ids[$key]), $key);
        }
    }

    public function testViewerCannotCloseTasks()
    {
        $this->createBoard(Role::PROJECT_VIEWER);
        $html = $this->confirmation('status:open needle');
        try {
            $this->submitConfirmation($html);
            $this->fail('Viewer must not close tasks');
        } catch (AccessForbiddenException $e) {
            $this->assertTrue($this->container['taskStatusModel']->isOpen($this->ids['visible']));
            $this->assertTrue($this->container['taskStatusModel']->isOpen($this->ids['hidden']));
        }
    }

    public function testColumnFromAnotherProjectIsRejected()
    {
        $this->createBoard();
        $html = $this->confirmation('status:open needle');
        $otherTask = $this->container['taskFinderModel']->getById($this->ids['project']);
        try {
            $this->submitConfirmation($html, array('column_id' => $otherTask['column_id']));
            $this->fail('A column from another project must be rejected');
        } catch (AccessForbiddenException $e) {
            $this->assertTrue($this->container['taskStatusModel']->isOpen($this->ids['project']));
            $this->assertTrue($this->container['taskStatusModel']->isOpen($this->ids['visible']));
        }
    }

    public function testSwimlaneFromAnotherProjectIsRejected()
    {
        $this->createBoard();
        $html = $this->confirmation('status:open needle');
        $otherTask = $this->container['taskFinderModel']->getById($this->ids['project']);
        try {
            $this->submitConfirmation($html, array('swimlane_id' => $otherTask['swimlane_id']));
            $this->fail('A swimlane from another project must be rejected');
        } catch (AccessForbiddenException $e) {
            $this->assertTrue($this->container['taskStatusModel']->isOpen($this->ids['project']));
            $this->assertTrue($this->container['taskStatusModel']->isOpen($this->ids['visible']));
        }
    }
}
