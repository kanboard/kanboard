<?php

use Kanboard\Decorator\MetadataCacheDecorator;

require_once __DIR__.'/../Base.php';

class MetadataCacheDecoratorTest extends Base
{
    protected $cachePrefix = 'cache_prefix';
    protected $entityId = 123;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataModelMock;

    /**
     * @var MetadataCacheDecorator
     */
    protected $metadataCacheDecorator;

    public function setUp()
    {
        parent::setUp();

        $this->cacheMock = $this
            ->getMockBuilder('\Kanboard\Core\Cache\MemoryCache')
            ->setMethods(array(
                'set',
                'get',
            ))
            ->getMock();

        $this->metadataModelMock = $this
            ->getMockBuilder('\Kanboard\Model\UserMetadataModel')
            ->setConstructorArgs(array($this->container))
            ->setMethods(array(
                'getAll',
                'save',
            ))
            ->getMock()
        ;

        $this->metadataCacheDecorator = new MetadataCacheDecorator(
            $this->cacheMock,
            $this->metadataModelMock,
            $this->cachePrefix,
            $this->entityId
        );
    }

    public function testSet()
    {
        $this->cacheMock
            ->expects($this->once())
            ->method('set');

        $this->metadataModelMock
            ->expects($this->at(0))
            ->method('save');

        $this->metadataModelMock
            ->expects($this->at(1))
            ->method('getAll')
            ->with($this->entityId)
        ;

        $this->metadataCacheDecorator->set('key', 'value');
    }

    public function testGetWithCache()
    {
        $this->cacheMock
            ->expects($this->once())
            ->method('get')
            ->with($this->cachePrefix.$this->entityId)
            ->will($this->returnValue(array('key' => 'foobar')))
        ;

        $this->assertEquals('foobar', $this->metadataCacheDecorator->get('key', 'default'));
    }

    public function testGetWithCacheAndDefaultValue()
    {
        $this->cacheMock
            ->expects($this->once())
            ->method('get')
            ->with($this->cachePrefix.$this->entityId)
            ->will($this->returnValue(array('key1' => 'foobar')))
        ;

        $this->assertEquals('default', $this->metadataCacheDecorator->get('key', 'default'));
    }

    public function testGetWithoutCache()
    {
        $this->cacheMock
            ->expects($this->at(0))
            ->method('get')
            ->with($this->cachePrefix.$this->entityId)
            ->will($this->returnValue(null))
        ;

        $this->cacheMock
            ->expects($this->at(1))
            ->method('set')
            ->with(
                $this->cachePrefix.$this->entityId,
                array('key' => 'something')
            )
        ;

        $this->metadataModelMock
            ->expects($this->once())
            ->method('getAll')
            ->with($this->entityId)
            ->will($this->returnValue(array('key' => 'something')))
        ;

        $this->assertEquals('something', $this->metadataCacheDecorator->get('key', 'default'));
    }
}
