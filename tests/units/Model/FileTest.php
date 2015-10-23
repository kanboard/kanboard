<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Task;
use Kanboard\Model\File;
use Kanboard\Model\TaskCreation;
use Kanboard\Model\Project;

class FileTest extends Base
{
    public function setUp()
    {
        parent::setUp();

        $this->container['objectStorage'] = $this
            ->getMockBuilder('\Kanboard\Core\ObjectStorage\FileStorage')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('put', 'moveFile', 'remove'))
            ->getMock();
    }

    public function testCreation()
    {
        $p = new Project($this->container);
        $f = new File($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test')));

        $this->assertEquals(1, $f->create(1, 'test', '/tmp/foo', 10));

        $file = $f->getById(1);
        $this->assertNotEmpty($file);
        $this->assertEquals('test', $file['name']);
        $this->assertEquals('/tmp/foo', $file['path']);
        $this->assertEquals(0, $file['is_image']);
        $this->assertEquals(1, $file['task_id']);
        $this->assertEquals(time(), $file['date'], '', 2);
        $this->assertEquals(0, $file['user_id']);
        $this->assertEquals(10, $file['size']);

        $this->assertEquals(2, $f->create(1, 'test2.png', '/tmp/foobar', 10));

        $file = $f->getById(2);
        $this->assertNotEmpty($file);
        $this->assertEquals('test2.png', $file['name']);
        $this->assertEquals('/tmp/foobar', $file['path']);
        $this->assertEquals(1, $file['is_image']);
    }

    public function testCreationFileNameTooLong()
    {
        $p = new Project($this->container);
        $f = new File($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test')));

        $this->assertNotFalse($f->create(1, 'test', '/tmp/foo', 10));
        $this->assertNotFalse($f->create(1, str_repeat('a', 1000), '/tmp/foo', 10));

        $files = $f->getAll(1);
        $this->assertNotEmpty($files);
        $this->assertCount(2, $files);

        $this->assertEquals(str_repeat('a', 255), $files[0]['name']);
        $this->assertEquals('test', $files[1]['name']);
    }

    public function testIsImage()
    {
        $f = new File($this->container);

        $this->assertTrue($f->isImage('test.png'));
        $this->assertTrue($f->isImage('test.jpeg'));
        $this->assertTrue($f->isImage('test.gif'));
        $this->assertTrue($f->isImage('test.jpg'));
        $this->assertTrue($f->isImage('test.JPG'));

        $this->assertFalse($f->isImage('test.bmp'));
        $this->assertFalse($f->isImage('test'));
        $this->assertFalse($f->isImage('test.pdf'));
    }

    public function testGeneratePath()
    {
        $f = new File($this->container);

        $this->assertStringStartsWith('12'.DIRECTORY_SEPARATOR.'34'.DIRECTORY_SEPARATOR, $f->generatePath(12, 34, 'test.png'));
        $this->assertNotEquals($f->generatePath(12, 34, 'test1.png'), $f->generatePath(12, 34, 'test2.png'));
    }

    public function testUploadScreenshot()
    {
        $p = new Project($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test')));

        $data = base64_encode('image data');

        $f = $this
            ->getMockBuilder('\Kanboard\Model\File')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('generateThumbnailFromData'))
            ->getMock();

        $this->container['objectStorage']
            ->expects($this->once())
            ->method('put')
            ->with(
                $this->stringContains('1'.DIRECTORY_SEPARATOR.'1'.DIRECTORY_SEPARATOR),
                $this->equalTo(base64_decode($data))
            )
            ->will($this->returnValue(true));

        $f->expects($this->once())
            ->method('generateThumbnailFromData');

        $this->assertEquals(1, $f->uploadScreenshot(1, 1, $data));

        $file = $f->getById(1);
        $this->assertNotEmpty($file);
        $this->assertStringStartsWith('Screenshot taken ', $file['name']);
        $this->assertStringStartsWith('1'.DIRECTORY_SEPARATOR.'1'.DIRECTORY_SEPARATOR, $file['path']);
        $this->assertEquals(1, $file['is_image']);
        $this->assertEquals(1, $file['task_id']);
        $this->assertEquals(time(), $file['date'], '', 2);
        $this->assertEquals(0, $file['user_id']);
        $this->assertEquals(10, $file['size']);
    }

    public function testUploadFileContent()
    {
        $p = new Project($this->container);
        $f = new File($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test')));

        $data = base64_encode('file data');

        $this->container['objectStorage']
            ->expects($this->once())
            ->method('put')
            ->with(
                $this->stringContains('1'.DIRECTORY_SEPARATOR.'1'.DIRECTORY_SEPARATOR),
                $this->equalTo(base64_decode($data))
            )
            ->will($this->returnValue(true));

        $this->assertEquals(1, $f->uploadContent(1, 1, 'my file.pdf', $data));

        $file = $f->getById(1);
        $this->assertNotEmpty($file);
        $this->assertEquals('my file.pdf', $file['name']);
        $this->assertStringStartsWith('1'.DIRECTORY_SEPARATOR.'1'.DIRECTORY_SEPARATOR, $file['path']);
        $this->assertEquals(0, $file['is_image']);
        $this->assertEquals(1, $file['task_id']);
        $this->assertEquals(time(), $file['date'], '', 2);
        $this->assertEquals(0, $file['user_id']);
        $this->assertEquals(9, $file['size']);
    }

    public function testGetAll()
    {
        $p = new Project($this->container);
        $f = new File($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test')));

        $this->assertEquals(1, $f->create(1, 'B.pdf', '/tmp/foo', 10));
        $this->assertEquals(2, $f->create(1, 'A.png', '/tmp/foo', 10));
        $this->assertEquals(3, $f->create(1, 'D.doc', '/tmp/foo', 10));
        $this->assertEquals(4, $f->create(1, 'C.JPG', '/tmp/foo', 10));

        $files = $f->getAll(1);
        $this->assertNotEmpty($files);
        $this->assertCount(4, $files);
        $this->assertEquals('A.png', $files[0]['name']);
        $this->assertEquals('B.pdf', $files[1]['name']);
        $this->assertEquals('C.JPG', $files[2]['name']);
        $this->assertEquals('D.doc', $files[3]['name']);

        $files = $f->getAllImages(1);
        $this->assertNotEmpty($files);
        $this->assertCount(2, $files);
        $this->assertEquals('A.png', $files[0]['name']);
        $this->assertEquals('C.JPG', $files[1]['name']);

        $files = $f->getAllDocuments(1);
        $this->assertNotEmpty($files);
        $this->assertCount(2, $files);
        $this->assertEquals('B.pdf', $files[0]['name']);
        $this->assertEquals('D.doc', $files[1]['name']);
    }

    public function testRemove()
    {
        $p = new Project($this->container);
        $f = new File($this->container);
        $tc = new TaskCreation($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'test')));
        $this->assertEquals(1, $tc->create(array('project_id' => 1, 'title' => 'test')));

        $this->assertEquals(1, $f->create(1, 'B.pdf', DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'foo1', 10));
        $this->assertEquals(2, $f->create(1, 'A.png', DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'foo2', 10));
        $this->assertEquals(3, $f->create(1, 'D.doc', DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'foo3', 10));

        $this->container['objectStorage']
            ->expects($this->at(0))
            ->method('remove')
            ->with(
                $this->equalTo(DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'foo2')
            )
            ->will($this->returnValue(true));

        $this->container['objectStorage']
            ->expects($this->at(1))
            ->method('remove')
            ->with(
                $this->equalTo('thumbnails'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'foo2')
            )
            ->will($this->returnValue(true));

        $this->container['objectStorage']
            ->expects($this->at(2))
            ->method('remove')
            ->with(
                $this->equalTo(DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'foo1')
            )
            ->will($this->returnValue(true));

        $this->container['objectStorage']
            ->expects($this->at(3))
            ->method('remove')
            ->with(
                $this->equalTo(DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'foo3')
            )
            ->will($this->returnValue(true));

        $this->assertTrue($f->remove(2));

        $files = $f->getAll(1);
        $this->assertNotEmpty($files);
        $this->assertCount(2, $files);
        $this->assertEquals('B.pdf', $files[0]['name']);
        $this->assertEquals('D.doc', $files[1]['name']);

        $this->assertTrue($f->removeAll(1));

        $files = $f->getAll(1);
        $this->assertEmpty($files);
    }
}
