<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\Session;
use Kanboard\Helper\App;
use Kanboard\Model\Config;

class AppHelperTest extends Base
{
    public function testJsLang()
    {
        $h = new App($this->container);
        $this->assertEquals('en', $h->jsLang());
    }

    public function testTimezone()
    {
        $h = new App($this->container);
        $this->assertEquals('UTC', $h->getTimezone());
    }

    public function testFlashMessage()
    {
        $h = new App($this->container);
        $s = new Session;

        $this->assertEmpty($h->flashMessage());
        $s->flash('test & test');
        $this->assertEquals('<div class="alert alert-success alert-fade-out">test &amp; test</div>', $h->flashMessage());
        $this->assertEmpty($h->flashMessage());

        $this->assertEmpty($h->flashMessage());
        $s->flashError('test & test');
        $this->assertEquals('<div class="alert alert-error">test &amp; test</div>', $h->flashMessage());
        $this->assertEmpty($h->flashMessage());
    }
}
