<?php

namespace KanboardTests\units\ExternalLink;

use KanboardTests\units\Base;
use Kanboard\ExternalLink\FileLink;

class FileLinkTest extends Base
{
    public function testGetTitleFromUrlWithUnixPath()
    {
        $url = 'file:///tmp/test.txt';

        $link = new FileLink($this->container);
        $link->setUrl($url);
        $this->assertEquals($url, $link->getUrl());
        $this->assertEquals('test.txt', $link->getTitle());
    }

    public function testGetTitleFromUrlWithWindowsPath()
    {
        $url = 'file:///c:\temp\test.txt';

        $link = new FileLink($this->container);
        $link->setUrl($url);
        $this->assertEquals($url, $link->getUrl());
        $this->assertEquals('test.txt', $link->getTitle());
    }
}
