<?php

namespace KanboardTests\units\Core\Session;

use KanboardTests\units\Base;
use Kanboard\Core\Http\Request;
use Kanboard\Core\Session\SessionManager;

class SessionManagerTest extends Base
{
    public function testCookiePathAtRoot()
    {
        $this->assertEquals('/', $this->buildSessionManager('/index.php')->getCookiePath());
    }

    public function testCookiePathInSubDirectoryHasNoTrailingSlash()
    {
        $this->assertEquals('/kanboard', $this->buildSessionManager('/kanboard/index.php')->getCookiePath());
    }

    public function testCookiePathInNestedSubDirectoryHasNoTrailingSlash()
    {
        $this->assertEquals('/apps/kanboard', $this->buildSessionManager('/apps/kanboard/index.php')->getCookiePath());
    }

    private function buildSessionManager($phpSelf)
    {
        $this->container['request'] = new Request(
            $this->container,
            array(
                'PHP_SELF' => $phpSelf,
                'REQUEST_METHOD' => 'GET',
            )
        );

        return new SessionManager($this->container);
    }
}
