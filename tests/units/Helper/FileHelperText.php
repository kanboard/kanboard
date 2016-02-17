<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\File;

class FileHelperTest extends Base
{
    public function testIcon()
    {
        $helper = new File($this->container);
        $this->assertEquals('fa-file-image-o', $helper->icon('test.png'));
        $this->assertEquals('fa-file-o', $helper->icon('test'));
    }

    public function testGetMimeType()
    {
        $helper = new File($this->container);

        $this->assertEquals('image/jpeg', $helper->getImageMimeType('My File.JPG'));
        $this->assertEquals('image/jpeg', $helper->getImageMimeType('My File.jpeg'));
        $this->assertEquals('image/png', $helper->getImageMimeType('My File.PNG'));
        $this->assertEquals('image/gif', $helper->getImageMimeType('My File.gif'));
        $this->assertEquals('image/jpeg', $helper->getImageMimeType('My File.bmp'));
        $this->assertEquals('image/jpeg', $helper->getImageMimeType('My File'));
    }
}
