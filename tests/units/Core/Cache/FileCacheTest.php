<?php

namespace Kanboard\Core\Cache;

require_once __DIR__.'/../../Base.php';

function file_put_contents($filename, $data)
{
    return FileCacheTest::$functions->file_put_contents($filename, $data);
}

function file_get_contents($filename)
{
    return FileCacheTest::$functions->file_get_contents($filename);
}

function mkdir($filename, $mode = 0777, $recursif = false)
{
    return FileCacheTest::$functions->mkdir($filename, $mode, $recursif);
}

function is_dir($filename)
{
    return FileCacheTest::$functions->is_dir($filename);
}

function file_exists($filename)
{
    return FileCacheTest::$functions->file_exists($filename);
}

function unlink($filename)
{
    return FileCacheTest::$functions->unlink($filename);
}

class FileCacheTest extends \Base
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
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
            ))
            ->getMock();
    }

    public function tearDown()
    {
        parent::tearDown();
        self::$functions = null;
    }

    public function testSet()
    {
        $key = 'mykey';
        $data = 'data';
        $cache = new FileCache();

        self::$functions
            ->expects($this->at(0))
            ->method('is_dir')
            ->with(
                $this->equalTo(CACHE_DIR)
            )
            ->will($this->returnValue(false));

        self::$functions
            ->expects($this->at(1))
            ->method('mkdir')
            ->with(
                $this->equalTo(CACHE_DIR),
                0755
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->at(2))
            ->method('file_put_contents')
            ->with(
                $this->equalTo(CACHE_DIR.DIRECTORY_SEPARATOR.$key),
                $this->equalTo(serialize($data))
            )
            ->will($this->returnValue(true));

        $cache->set($key, $data);
    }

    public function testGet()
    {
        $key = 'mykey';
        $data = 'data';
        $cache = new FileCache();

        self::$functions
            ->expects($this->at(0))
            ->method('file_exists')
            ->with(
                $this->equalTo(CACHE_DIR.DIRECTORY_SEPARATOR.$key)
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->at(1))
            ->method('file_get_contents')
            ->with(
                $this->equalTo(CACHE_DIR.DIRECTORY_SEPARATOR.$key)
            )
            ->will($this->returnValue(serialize($data)));

        $this->assertSame($data, $cache->get($key));
    }

    public function testGetWithKeyNotFound()
    {
        $key = 'mykey';
        $cache = new FileCache();

        self::$functions
            ->expects($this->at(0))
            ->method('file_exists')
            ->with(
                $this->equalTo(CACHE_DIR.DIRECTORY_SEPARATOR.$key)
            )
            ->will($this->returnValue(false));

        $this->assertNull($cache->get($key));
    }

    public function testRemoveWithKeyNotFound()
    {
        $key = 'mykey';
        $cache = new FileCache();

        self::$functions
            ->expects($this->at(0))
            ->method('file_exists')
            ->with(
                $this->equalTo(CACHE_DIR.DIRECTORY_SEPARATOR.$key)
            )
            ->will($this->returnValue(false));

        self::$functions
            ->expects($this->never())
            ->method('unlink');

        $cache->remove($key);
    }

    public function testRemove()
    {
        $key = 'mykey';
        $cache = new FileCache();

        self::$functions
            ->expects($this->at(0))
            ->method('file_exists')
            ->with(
                $this->equalTo(CACHE_DIR.DIRECTORY_SEPARATOR.$key)
            )
            ->will($this->returnValue(true));

        self::$functions
            ->expects($this->at(1))
            ->method('unlink')
            ->with(
                $this->equalTo(CACHE_DIR.DIRECTORY_SEPARATOR.$key)
            )
            ->will($this->returnValue(true));

        $cache->remove($key);
    }
}
