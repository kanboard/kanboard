<?php

use Kanboard\Model\ProjectModel;
use Kanboard\Model\TagDuplicationModel;
use Kanboard\Model\TagModel;

require_once __DIR__.'/../Base.php';

class TagDuplicationModelTest extends Base
{
    public function testProjectDuplication()
    {
        $tagModel = new TagModel($this->container);
        $tagDuplicationModel = new TagDuplicationModel($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'P1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'P2')));

        $this->assertEquals(1, $tagModel->create(0, 'Tag 1'));
        $this->assertEquals(2, $tagModel->create(1, 'Tag 2'));
        $this->assertEquals(3, $tagModel->create(1, 'Tag 3', 'green'));

        $this->assertTrue($tagDuplicationModel->duplicate(1, 2));

        $tags = $tagModel->getAllByProject(2);
        $this->assertCount(2, $tags);
        $this->assertEquals('Tag 2', $tags[0]['name']);
        $this->assertEquals('', $tags[0]['color_id']);
        $this->assertEquals('Tag 3', $tags[1]['name']);
        $this->assertEquals('green', $tags[1]['color_id']);
    }
}
