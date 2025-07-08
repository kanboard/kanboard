<?php

namespace KanboardTests\units\ExternalLink;

use KanboardTests\units\Base;
use Kanboard\ExternalLink\AttachmentLink;

class AttachmentLinkTest extends Base
{
    public function testGetTitleFromUrl()
    {
        $url = 'https://kanboard.org/folder/document.pdf';

        $link = new AttachmentLink($this->container);
        $link->setUrl($url);
        $this->assertEquals($url, $link->getUrl());
        $this->assertEquals('document.pdf', $link->getTitle());
    }
}
