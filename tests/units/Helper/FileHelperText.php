<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Helper\File;

class FileHelperTest extends Base
{
    public function testIcon()
    {
        $h = new File($this->container);
        $this->assertEquals('fa-file-image-o', $h->icon('test.png'));
        $this->assertEquals('fa-file-o', $h->icon('test'));
    }
}
