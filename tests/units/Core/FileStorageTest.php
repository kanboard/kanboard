<?php

namespace Kanboard\Core\ObjectStorage;

require_once __DIR__.'/../Base.php';

function file_put_contents($filename, $data)
{
    return FileStorageTest::$functions->file_put_contents($filename, $data);
}

function file_get_contents($filename)
{
    return FileStorageTest::$functions->file_get_contents($filename);
}

function mkdir($filename, $mode, $recursif)
{
    return FileStorageTest::$functions->mkdir($filename, $mode, $recursif);
}

function is_dir($filename)
{
    return FileStorageTest::$functions->is_dir($filename);
}

function file_exists($filename)
{
    return FileStorageTest::$functions->file_exists($filename);
}

function unlink($filename)
{
    return FileStorageTest::$functions->unlink($filename);
}

function readfile($filename)
{
    echo FileStorageTest::$functions->readfile($filename);
}

function rename($src, $dst)
{
    return FileStorageTest::$functions->rename($src, $dst);
}

function move_uploaded_file($src, $dst)
{
    return FileStorageTest::$functions->move_uploaded_file($src, $dst);
}

class FileStorageTest extends \Base
{
    public static $functions;

    public function setUp()
    {
        parent::setup();

        self::$functions = $this
            ->getMockBuilder('stdClass')
            ->setMethods(array(
                'file_put_contents',
                'file_get_contents',
                'file_exists',
                'mkdir',
                'is_dir',
                'unlink',
                'rename',
                'move_uploaded_file',
                'readfile',
            ))
            ->getMock();
    }

    public function tearDown()
    {
        parent::tearDown();
        self::$functions = null;
    }

    public function testPut()
    {
        $data = 'data';
        $storage = new FileStorage('somewhere');

        self::$functions
            ->expects($this->at(0))
            ->method('is_dir')
            ->with(
                $this->equalTo('somewhere')
            )
            ->will($this->returnValue(false));

        self::$functions
            ->expects($this->at(1))
            ->method('mkdir')
            ->with(
                $this->equalTo('somewhere')
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->at(2))
            ->method('file_put_contents')
            ->with(
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'mykey'),
                $this->equalTo('data')
            )
            ->will($this->returnValue(true));

        $storage->put('mykey', $data);
    }

    public function testPutWithSubfolder()
    {
        $data = 'data';
        $storage = new FileStorage('somewhere');

        self::$functions
            ->expects($this->at(0))
            ->method('is_dir')
            ->with(
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'my')
            )
            ->will($this->returnValue(false));

        self::$functions
            ->expects($this->at(1))
            ->method('mkdir')
            ->with(
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'my')
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->at(2))
            ->method('file_put_contents')
            ->with(
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'my'.DIRECTORY_SEPARATOR.'key'),
                $this->equalTo('data')
            )
            ->will($this->returnValue(true));

        $storage->put('my'.DIRECTORY_SEPARATOR.'key', $data);
    }

    /**
     * @expectedException \Kanboard\Core\ObjectStorage\ObjectStorageException
     */
    public function testPutWhenNotAbleToCreateFolder()
    {
        $data = 'data';
        $storage = new FileStorage('somewhere');

        self::$functions
            ->expects($this->at(0))
            ->method('is_dir')
            ->with(
                $this->equalTo('somewhere')
            )
            ->will($this->returnValue(false));

        self::$functions
            ->expects($this->at(1))
            ->method('mkdir')
            ->with(
                $this->equalTo('somewhere')
            )
            ->will($this->returnValue(false));

        $storage->put('mykey', $data);
    }

    public function testGet()
    {
        $storage = new FileStorage('somewhere');

        self::$functions
            ->expects($this->at(0))
            ->method('file_exists')
            ->with(
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'mykey')
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->at(1))
            ->method('file_get_contents')
            ->with(
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'mykey')
            )
            ->will($this->returnValue('data'));

        $this->assertEquals('data', $storage->get('mykey'));
    }

    /**
     * @expectedException \Kanboard\Core\ObjectStorage\ObjectStorageException
     */
    public function testGetWithFileNotFound()
    {
        $storage = new FileStorage('somewhere');

        self::$functions
            ->expects($this->at(0))
            ->method('file_exists')
            ->with(
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'mykey')
            )
            ->will($this->returnValue(false));

        $this->assertEquals('data', $storage->get('mykey'));
    }

    public function testOutput()
    {
        $storage = new FileStorage('somewhere');

        self::$functions
            ->expects($this->at(0))
            ->method('file_exists')
            ->with(
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'mykey')
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->at(1))
            ->method('readfile')
            ->with(
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'mykey')
            )
            ->will($this->returnValue('data'));

        $this->expectOutputString('data');
        $storage->output('mykey');
    }

    public function testRemove()
    {
        $storage = new FileStorage('somewhere');

        self::$functions
            ->expects($this->at(0))
            ->method('file_exists')
            ->with(
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'mykey')
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->at(1))
            ->method('unlink')
            ->with(
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'mykey')
            )
            ->will($this->returnValue(true));

        $this->assertTrue($storage->remove('mykey'));
    }

    public function testMoveFile()
    {
        $storage = new FileStorage('somewhere');

        self::$functions
            ->expects($this->at(0))
            ->method('is_dir')
            ->with(
                $this->equalTo('somewhere')
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->at(1))
            ->method('rename')
            ->with(
                $this->equalTo('src_file'),
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'mykey')
            )
            ->will($this->returnValue(true));

        $this->assertTrue($storage->moveFile('src_file', 'mykey'));
    }

    public function testMoveUploadedFile()
    {
        $storage = new FileStorage('somewhere');

        self::$functions
            ->expects($this->at(0))
            ->method('is_dir')
            ->with(
                $this->equalTo('somewhere')
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->at(1))
            ->method('move_uploaded_file')
            ->with(
                $this->equalTo('src_file'),
                $this->equalTo('somewhere'.DIRECTORY_SEPARATOR.'mykey')
            )
            ->will($this->returnValue(true));

        $this->assertTrue($storage->moveUploadedFile('src_file', 'mykey'));
    }
}
