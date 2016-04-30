<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskCreation;
use Kanboard\Model\Project;
use Kanboard\Model\TaskExternalLink;
use Kanboard\Core\ExternalLink\ExternalLinkManager;
use Kanboard\ExternalLink\WebLinkProvider;

class TaskExternalLinkTest extends Base
{
    public function testCreate()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskExternalLinkModel = new TaskExternalLink($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $taskExternalLinkModel->create(array('task_id' => 1, 'id' => '', 'url' => 'https://kanboard.net/', 'title' => 'My website', 'link_type' => 'weblink', 'dependency' => 'related')));

        $link = $taskExternalLinkModel->getById(1);
        $this->assertNotEmpty($link);
        $this->assertEquals('My website', $link['title']);
        $this->assertEquals('https://kanboard.net/', $link['url']);
        $this->assertEquals('related', $link['dependency']);
        $this->assertEquals('weblink', $link['link_type']);
        $this->assertEquals(0, $link['creator_id']);
        $this->assertEquals(time(), $link['date_modification'], '', 2);
        $this->assertEquals(time(), $link['date_creation'], '', 2);
    }

    public function testCreateWithUserSession()
    {
        $this->container['sessionStorage']->user = array('id' => 1);

        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskExternalLinkModel = new TaskExternalLink($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $taskExternalLinkModel->create(array('task_id' => 1, 'id' => '', 'url' => 'https://kanboard.net/', 'title' => 'My website', 'link_type' => 'weblink', 'dependency' => 'related')));

        $link = $taskExternalLinkModel->getById(1);
        $this->assertNotEmpty($link);
        $this->assertEquals('My website', $link['title']);
        $this->assertEquals('https://kanboard.net/', $link['url']);
        $this->assertEquals('related', $link['dependency']);
        $this->assertEquals('weblink', $link['link_type']);
        $this->assertEquals(1, $link['creator_id']);
        $this->assertEquals(time(), $link['date_modification'], '', 2);
        $this->assertEquals(time(), $link['date_creation'], '', 2);
    }

    public function testModification()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskExternalLinkModel = new TaskExternalLink($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $taskExternalLinkModel->create(array('task_id' => 1, 'id' => '', 'url' => 'https://kanboard.net/', 'title' => 'My website', 'link_type' => 'weblink', 'dependency' => 'related')));

        sleep(1);

        $this->assertTrue($taskExternalLinkModel->update(array('id' => 1, 'url' => 'https://kanboard.net/')));

        $link = $taskExternalLinkModel->getById(1);
        $this->assertNotEmpty($link);
        $this->assertEquals('https://kanboard.net/', $link['url']);
        $this->assertEquals(time(), $link['date_modification'], '', 2);
    }

    public function testRemove()
    {
        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskExternalLinkModel = new TaskExternalLink($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $taskExternalLinkModel->create(array('task_id' => 1, 'id' => '', 'url' => 'https://kanboard.net/', 'title' => 'My website', 'link_type' => 'weblink', 'dependency' => 'related')));

        $this->assertTrue($taskExternalLinkModel->remove(1));
        $this->assertFalse($taskExternalLinkModel->remove(1));

        $this->assertEmpty($taskExternalLinkModel->getById(1));
    }

    public function testGetAll()
    {
        $this->container['sessionStorage']->user = array('id' => 1);
        $this->container['externalLinkManager'] = new ExternalLinkManager($this->container);

        $projectModel = new Project($this->container);
        $taskCreationModel = new TaskCreation($this->container);
        $taskExternalLinkModel = new TaskExternalLink($this->container);
        $webLinkProvider = new WebLinkProvider($this->container);

        $this->container['externalLinkManager']->register($webLinkProvider);

        $this->assertEquals(1, $projectModel->create(array('name' => 'Test')));
        $this->assertEquals(1, $taskCreationModel->create(array('title' => 'Test', 'project_id' => 1)));
        $this->assertEquals(1, $taskExternalLinkModel->create(array('task_id' => 1, 'url' => 'https://miniflux.net/', 'title' => 'MX', 'link_type' => 'weblink', 'dependency' => 'related')));
        $this->assertEquals(2, $taskExternalLinkModel->create(array('task_id' => 1, 'url' => 'https://kanboard.net/', 'title' => 'KB', 'link_type' => 'weblink', 'dependency' => 'related')));

        $links = $taskExternalLinkModel->getAll(1);
        $this->assertCount(2, $links);
        $this->assertEquals('KB', $links[0]['title']);
        $this->assertEquals('MX', $links[1]['title']);
        $this->assertEquals('Web Link', $links[0]['type']);
        $this->assertEquals('Web Link', $links[1]['type']);
        $this->assertEquals('Related', $links[0]['dependency_label']);
        $this->assertEquals('Related', $links[1]['dependency_label']);
        $this->assertEquals('admin', $links[0]['creator_username']);
        $this->assertEquals('admin', $links[1]['creator_username']);
        $this->assertEquals('', $links[0]['creator_name']);
        $this->assertEquals('', $links[1]['creator_name']);
    }
}
