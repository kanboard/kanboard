<?php

require_once __DIR__.'/Base.php';

class AppTest extends Base
{
    public function testGetTimezone()
    {
        $this->assertEquals('UTC', $this->app->getTimezone());
    }

    public function testGetVersion()
    {
        $this->assertEquals('master', $this->app->getVersion());
    }

    public function testGetApplicationRoles()
    {
        $roles = $this->app->getApplicationRoles();
        $this->assertCount(3, $roles);
        $this->assertEquals('Administrator', $roles['app-admin']);
        $this->assertEquals('Manager', $roles['app-manager']);
        $this->assertEquals('User', $roles['app-user']);
    }

    public function testGetProjectRoles()
    {
        $roles = $this->app->getProjectRoles();
        $this->assertCount(3, $roles);
        $this->assertEquals('Project Manager', $roles['project-manager']);
        $this->assertEquals('Project Member', $roles['project-member']);
        $this->assertEquals('Project Viewer', $roles['project-viewer']);
    }
}
