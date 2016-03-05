<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\Session\FlashMessage;
use Kanboard\Helper\AppHelper;

class AppHelperTest extends Base
{
    public function testJsLang()
    {
        $h = new AppHelper($this->container);
        $this->assertEquals('en', $h->jsLang());
    }

    public function testTimezone()
    {
        $h = new AppHelper($this->container);
        $this->assertEquals('UTC', $h->getTimezone());
    }

    public function testFlashMessage()
    {
        $h = new AppHelper($this->container);
        $f = new FlashMessage($this->container);

        $this->assertEmpty($h->flashMessage());

        $f->success('test & test');
        $this->assertEquals('<div class="alert alert-success alert-fade-out">test &amp; test</div>', $h->flashMessage());
        $this->assertEmpty($h->flashMessage());

        $f->failure('test & test');
        $this->assertEquals('<div class="alert alert-error">test &amp; test</div>', $h->flashMessage());
        $this->assertEmpty($h->flashMessage());
    }
}
