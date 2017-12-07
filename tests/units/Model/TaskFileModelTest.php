<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\TaskFileModel;
use Kanboard\Model\TaskCreationModel;
use Kanboard\Model\ProjectModel;

class TaskFileModelTest extends Base
{
    public function testCreation()
    {
        $projectModel = new ProjectModel($this->container);
        $fileModel = new TaskFileModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->assertEquals(1, $fileModel->create(1, 'test', '/tmp/foo', 10));

        $file = $fileModel->getById(1);
        $this->assertEquals('test', $file['name']);
        $this->assertEquals('/tmp/foo', $file['path']);
        $this->assertEquals(0, $file['is_image']);
        $this->assertEquals(1, $file['task_id']);
        $this->assertEquals(time(), $file['date'], '', 2);
        $this->assertEquals(0, $file['user_id']);
        $this->assertEquals(10, $file['size']);

        $this->assertEquals(2, $fileModel->create(1, 'test2.png', '/tmp/foobar', 10));

        $file = $fileModel->getById(2);
        $this->assertEquals('test2.png', $file['name']);
        $this->assertEquals('/tmp/foobar', $file['path']);
        $this->assertEquals(1, $file['is_image']);
    }

    public function testCreationWithFileNameTooLong()
    {
        $projectModel = new ProjectModel($this->container);
        $fileModel = new TaskFileModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->assertNotFalse($fileModel->create(1, 'test', '/tmp/foo', 10));
        $this->assertNotFalse($fileModel->create(1, str_repeat('a', 1000), '/tmp/foo', 10));

        $files = $fileModel->getAll(1);
        $this->assertNotEmpty($files);
        $this->assertCount(2, $files);

        $this->assertEquals(str_repeat('a', 255), $files[0]['name']);
        $this->assertEquals('test', $files[1]['name']);
    }

    public function testCreationWithSessionOpen()
    {
        $_SESSION['user'] = array('id' => 1);

        $projectModel = new ProjectModel($this->container);
        $fileModel = new TaskFileModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(1, $fileModel->create(1, 'test', '/tmp/foo', 10));

        $file = $fileModel->getById(1);
        $this->assertEquals('test', $file['name']);
        $this->assertEquals(1, $file['user_id']);
    }

    public function testGetAll()
    {
        $projectModel = new ProjectModel($this->container);
        $fileModel = new TaskFileModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->assertEquals(1, $fileModel->create(1, 'B.pdf', '/tmp/foo', 10));
        $this->assertEquals(2, $fileModel->create(1, 'A.png', '/tmp/foo', 10));
        $this->assertEquals(3, $fileModel->create(1, 'D.doc', '/tmp/foo', 10));
        $this->assertEquals(4, $fileModel->create(1, 'C.JPG', '/tmp/foo', 10));

        $fileModeliles = $fileModel->getAll(1);
        $this->assertNotEmpty($fileModeliles);
        $this->assertCount(4, $fileModeliles);
        $this->assertEquals('A.png', $fileModeliles[0]['name']);
        $this->assertEquals('B.pdf', $fileModeliles[1]['name']);
        $this->assertEquals('C.JPG', $fileModeliles[2]['name']);
        $this->assertEquals('D.doc', $fileModeliles[3]['name']);

        $fileModeliles = $fileModel->getAllImages(1);
        $this->assertNotEmpty($fileModeliles);
        $this->assertCount(2, $fileModeliles);
        $this->assertEquals('A.png', $fileModeliles[0]['name']);
        $this->assertEquals('C.JPG', $fileModeliles[1]['name']);

        $fileModeliles = $fileModel->getAllDocuments(1);
        $this->assertNotEmpty($fileModeliles);
        $this->assertCount(2, $fileModeliles);
        $this->assertEquals('B.pdf', $fileModeliles[0]['name']);
        $this->assertEquals('D.doc', $fileModeliles[1]['name']);
    }

    public function testIsImage()
    {
        $fileModel = new TaskFileModel($this->container);

        $this->assertTrue($fileModel->isImage('test.png'));
        $this->assertTrue($fileModel->isImage('test.jpeg'));
        $this->assertTrue($fileModel->isImage('test.gif'));
        $this->assertTrue($fileModel->isImage('test.jpg'));
        $this->assertTrue($fileModel->isImage('test.JPG'));

        $this->assertFalse($fileModel->isImage('test.bmp'));
        $this->assertFalse($fileModel->isImage('test'));
        $this->assertFalse($fileModel->isImage('test.pdf'));
    }

    public function testGetThumbnailPath()
    {
        $fileModel = new TaskFileModel($this->container);
        $this->assertEquals('thumbnails'.DIRECTORY_SEPARATOR.'test', $fileModel->getThumbnailPath('test'));
    }

    public function testGeneratePath()
    {
        $fileModel = new TaskFileModel($this->container);

        $this->assertStringStartsWith('tasks'.DIRECTORY_SEPARATOR.'34'.DIRECTORY_SEPARATOR, $fileModel->generatePath(34, 'test.png'));
        $this->assertNotEquals($fileModel->generatePath(34, 'test1.png'), $fileModel->generatePath(34, 'test2.png'));
    }

    public function testUploadFiles()
    {
        $fileModel = $this
            ->getMockBuilder('\Kanboard\Model\TaskFileModel')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('generateThumbnailFromFile'))
            ->getMock();

        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $files = array(
            'name' => array(
                'file1.png',
                'file2.doc',
            ),
            'tmp_name' => array(
                '/tmp/phpYzdqkD',
                '/tmp/phpeEwEWG',
            ),
            'error' => array(
                UPLOAD_ERR_OK,
                UPLOAD_ERR_OK,
            ),
            'size' => array(
                123,
                456,
            ),
        );

        $fileModel
            ->expects($this->once())
            ->method('generateThumbnailFromFile');

        $this->container['objectStorage']
            ->expects($this->at(0))
            ->method('moveUploadedFile')
            ->with($this->equalTo('/tmp/phpYzdqkD'), $this->anything());

        $this->container['objectStorage']
            ->expects($this->at(1))
            ->method('moveUploadedFile')
            ->with($this->equalTo('/tmp/phpeEwEWG'), $this->anything());

        $this->assertTrue($fileModel->uploadFiles(1, $files));

        $files = $fileModel->getAll(1);
        $this->assertCount(2, $files);

        $this->assertEquals(1, $files[0]['id']);
        $this->assertEquals('file1.png', $files[0]['name']);
        $this->assertEquals(1, $files[0]['is_image']);
        $this->assertEquals(1, $files[0]['task_id']);
        $this->assertEquals(0, $files[0]['user_id']);
        $this->assertEquals(123, $files[0]['size']);
        $this->assertEquals(time(), $files[0]['date'], '', 2);

        $this->assertEquals(2, $files[1]['id']);
        $this->assertEquals('file2.doc', $files[1]['name']);
        $this->assertEquals(0, $files[1]['is_image']);
        $this->assertEquals(1, $files[1]['task_id']);
        $this->assertEquals(0, $files[1]['user_id']);
        $this->assertEquals(456, $files[1]['size']);
        $this->assertEquals(time(), $files[1]['date'], '', 2);
    }

    public function testUploadFilesWithEmptyFiles()
    {
        $fileModel = new TaskFileModel($this->container);
        $this->assertFalse($fileModel->uploadFiles(1, array()));
    }

    public function testUploadFilesWithUploadError()
    {
        $files = array(
            'name' => array(
                'file1.png',
                'file2.doc',
            ),
            'tmp_name' => array(
                '',
                '/tmp/phpeEwEWG',
            ),
            'error' => array(
                UPLOAD_ERR_CANT_WRITE,
                UPLOAD_ERR_OK,
            ),
            'size' => array(
                123,
                456,
            ),
        );

        $fileModel = new TaskFileModel($this->container);
        $this->assertFalse($fileModel->uploadFiles(1, $files));
    }

    public function testUploadFilesWithObjectStorageError()
    {
        $files = array(
            'name' => array(
                'file1.csv',
                'file2.doc',
            ),
            'tmp_name' => array(
                '/tmp/phpYzdqkD',
                '/tmp/phpeEwEWG',
            ),
            'error' => array(
                UPLOAD_ERR_OK,
                UPLOAD_ERR_OK,
            ),
            'size' => array(
                123,
                456,
            ),
        );

        $this->container['objectStorage']
            ->expects($this->at(0))
            ->method('moveUploadedFile')
            ->with($this->equalTo('/tmp/phpYzdqkD'), $this->anything())
            ->will($this->throwException(new \Kanboard\Core\ObjectStorage\ObjectStorageException('test')));

        $fileModel = new TaskFileModel($this->container);
        $this->assertFalse($fileModel->uploadFiles(1, $files));
    }

    public function testUploadFileContent()
    {
        $fileModel = $this
            ->getMockBuilder('\Kanboard\Model\TaskFileModel')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('generateThumbnailFromFile'))
            ->getMock();

        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $data = 'test';

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->container['objectStorage']
            ->expects($this->once())
            ->method('put')
            ->with($this->anything(), $this->equalTo($data));

        $this->assertEquals(1, $fileModel->uploadContent(1, 'test.doc', base64_encode($data)));

        $files = $fileModel->getAll(1);
        $this->assertCount(1, $files);

        $this->assertEquals(1, $files[0]['id']);
        $this->assertEquals('test.doc', $files[0]['name']);
        $this->assertEquals(0, $files[0]['is_image']);
        $this->assertEquals(1, $files[0]['task_id']);
        $this->assertEquals(0, $files[0]['user_id']);
        $this->assertEquals(4, $files[0]['size']);
        $this->assertEquals(time(), $files[0]['date'], '', 2);
    }

    public function testUploadFileContentWithObjectStorageError()
    {
        $fileModel = $this
            ->getMockBuilder('\Kanboard\Model\TaskFileModel')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('generateThumbnailFromFile'))
            ->getMock();

        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $data = 'test';

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $this->container['objectStorage']
            ->expects($this->once())
            ->method('put')
            ->with($this->anything(), $this->equalTo($data))
            ->will($this->throwException(new \Kanboard\Core\ObjectStorage\ObjectStorageException('test')));

        $this->assertFalse($fileModel->uploadContent(1, 'test.doc', base64_encode($data)));
    }

    public function testUploadScreenshot()
    {
        $fileModel = $this
            ->getMockBuilder('\Kanboard\Model\TaskFileModel')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array('generateThumbnailFromData'))
            ->getMock();

        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);
        $data = 'test';

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));

        $fileModel
            ->expects($this->once())
            ->method('generateThumbnailFromData');

        $this->container['objectStorage']
            ->expects($this->once())
            ->method('put')
            ->with($this->anything(), $this->equalTo($data));

        $this->assertEquals(1, $fileModel->uploadScreenshot(1, base64_encode($data)));

        $files = $fileModel->getAll(1);
        $this->assertCount(1, $files);

        $this->assertEquals(1, $files[0]['id']);
        $this->assertStringStartsWith('Screenshot taken ', $files[0]['name']);
        $this->assertEquals(1, $files[0]['is_image']);
        $this->assertEquals(1, $files[0]['task_id']);
        $this->assertEquals(0, $files[0]['user_id']);
        $this->assertEquals(4, $files[0]['size']);
        $this->assertEquals(time(), $files[0]['date'], '', 2);
    }

    public function testRemove()
    {
        $fileModel = new TaskFileModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(1, $fileModel->create(1, 'test', 'tmp/foo', 10));

        $this->container['objectStorage']
            ->expects($this->once())
            ->method('remove')
            ->with('tmp/foo');

        $this->assertTrue($fileModel->remove(1));
    }

    public function testRemoveWithObjectStorageError()
    {
        $fileModel = new TaskFileModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(1, $fileModel->create(1, 'test', 'tmp/foo', 10));

        $this->container['objectStorage']
            ->expects($this->once())
            ->method('remove')
            ->with('tmp/foo')
            ->will($this->throwException(new \Kanboard\Core\ObjectStorage\ObjectStorageException('test')));

        $this->assertFalse($fileModel->remove(1));
    }

    public function testRemoveImage()
    {
        $fileModel = new TaskFileModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(1, $fileModel->create(1, 'image.gif', 'tmp/image.gif', 10));

        $this->container['objectStorage']
            ->expects($this->at(0))
            ->method('remove')
            ->with('tmp/image.gif');

        $this->container['objectStorage']
            ->expects($this->at(1))
            ->method('remove')
            ->with('thumbnails'.DIRECTORY_SEPARATOR.'tmp/image.gif');

        $this->assertTrue($fileModel->remove(1));
    }

    public function testRemoveAll()
    {
        $fileModel = new TaskFileModel($this->container);
        $projectModel = new ProjectModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(1, $fileModel->create(1, 'test', 'tmp/foo', 10));
        $this->assertEquals(2, $fileModel->create(1, 'test', 'tmp/foo', 10));

        $this->container['objectStorage']
            ->expects($this->exactly(2))
            ->method('remove')
            ->with('tmp/foo');

        $this->assertTrue($fileModel->removeAll(1));
    }

    public function testGetProjectId()
    {
        $projectModel = new ProjectModel($this->container);
        $fileModel = new TaskFileModel($this->container);
        $taskCreationModel = new TaskCreationModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'test')));
        $this->assertEquals(1, $taskCreationModel->create(array('project_id' => 1, 'title' => 'test')));
        $this->assertEquals(1, $fileModel->create(1, 'test', '/tmp/foobar', 10));
        $this->assertEquals(1, $fileModel->getProjectId(1));
        $this->assertEquals(0, $fileModel->getProjectId(2));
    }
}
