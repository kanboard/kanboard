<?php

require_once __DIR__.'/BaseProcedureTest.php';

class SubtaskTimeTrackingProcedureTest extends BaseProcedureTest
{
    protected $projectName = 'My project to test subtask time tracking';

    public function testAll()
    {
        $this->assertCreateTeamProject();
        $this->assertCreateTask();
        $this->assertCreateSubtask();
        $this->assertHasNoTimer();
        $this->assertStartTimer();
        $this->assertHasTimer();
        $this->assertStopTimer();
        $this->assertHasNoTimer();
        $this->assertGetSubtaskTimeSpent();
    }

    public function assertHasNoTimer()
    {
        $this->assertFalse($this->app->hasSubtaskTimer($this->subtaskId, $this->userUserId));
    }

    public function assertHasTimer()
    {
        $this->assertTrue($this->app->hasSubtaskTimer($this->subtaskId, $this->userUserId));
    }

    public function assertStartTimer()
    {
        $this->assertTrue($this->app->setSubtaskStartTime($this->subtaskId, $this->userUserId));
    }

    public function assertStopTimer()
    {
        $this->assertTrue($this->app->setSubtaskEndTime($this->subtaskId, $this->userUserId));
    }

    public function assertGetSubtaskTimeSpent()
    {
        $this->assertEquals(0, $this->app->getSubtaskTimeSpent($this->subtaskId, $this->userUserId));
    }
}
