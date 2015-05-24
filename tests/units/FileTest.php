<?php

require_once __DIR__.'/Base.php';

use Model\Task;
use Model\File;
use Model\TaskCreation;
use Model\Project;

class FileTest extends Base
{
    public function testCreationFileNameTooLong()
    {
        $p = new Project($this->container);
        $f = new File($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test')));

        $this->assertTrue($f->create(1, 'test', '/tmp/foo', false, 10));
        $this->assertTrue($f->create(1, str_repeat('a', 1000), '/tmp/foo', false, 10));

        $files = $f->getAll(1);
        $this->assertNotEmpty($files);
        $this->assertCount(2, $files);

        $this->assertEquals(str_repeat('a', 255), $files[0]['name']);
        $this->assertEquals('test', $files[1]['name']);
    }
}
