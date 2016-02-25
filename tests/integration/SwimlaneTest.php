<?php

require_once __DIR__.'/Base.php';

class SwimlaneTest extends Base
{
    public function testCreateProject()
    {
        $this->assertEquals(1, $this->app->createProject('A project'));
    }

    public function testGetDefaultSwimlane()
    {
        $swimlane = $this->app->getDefaultSwimlane(1);
        $this->assertNotEmpty($swimlane);
        $this->assertEquals('Default swimlane', $swimlane['default_swimlane']);
    }

    public function testAddSwimlane()
    {
        $swimlane_id = $this->app->addSwimlane(1, 'Swimlane 1');
        $this->assertNotFalse($swimlane_id);
        $this->assertInternalType('int', $swimlane_id);

        $swimlane = $this->app->getSwimlaneById($swimlane_id);
        $this->assertNotEmpty($swimlane);
        $this->assertInternalType('array', $swimlane);
        $this->assertEquals('Swimlane 1', $swimlane['name']);
    }

    public function testGetSwimlane()
    {
        $swimlane = $this->app->getSwimlane(1);
        $this->assertInternalType('array', $swimlane);
        $this->assertEquals('Swimlane 1', $swimlane['name']);
    }

    public function testUpdateSwimlane()
    {
        $swimlane = $this->app->getSwimlaneByName(1, 'Swimlane 1');
        $this->assertInternalType('array', $swimlane);
        $this->assertEquals(1, $swimlane['id']);
        $this->assertEquals('Swimlane 1', $swimlane['name']);

        $this->assertTrue($this->app->updateSwimlane($swimlane['id'], 'Another swimlane'));

        $swimlane = $this->app->getSwimlaneById($swimlane['id']);
        $this->assertEquals('Another swimlane', $swimlane['name']);
    }

    public function testDisableSwimlane()
    {
        $this->assertTrue($this->app->disableSwimlane(1, 1));

        $swimlane = $this->app->getSwimlaneById(1);
        $this->assertEquals(0, $swimlane['is_active']);
    }

    public function testEnableSwimlane()
    {
        $this->assertTrue($this->app->enableSwimlane(1, 1));

        $swimlane = $this->app->getSwimlaneById(1);
        $this->assertEquals(1, $swimlane['is_active']);
    }

    public function testGetAllSwimlanes()
    {
        $this->assertNotFalse($this->app->addSwimlane(1, 'Swimlane A'));

        $swimlanes = $this->app->getAllSwimlanes(1);
        $this->assertCount(2, $swimlanes);
        $this->assertEquals('Another swimlane', $swimlanes[0]['name']);
        $this->assertEquals('Swimlane A', $swimlanes[1]['name']);
    }

    public function testGetActiveSwimlane()
    {
        $this->assertTrue($this->app->disableSwimlane(1, 1));

        $swimlanes = $this->app->getActiveSwimlanes(1);
        $this->assertCount(2, $swimlanes);
        $this->assertEquals('Default swimlane', $swimlanes[0]['name']);
        $this->assertEquals('Swimlane A', $swimlanes[1]['name']);
    }

    public function testRemoveSwimlane()
    {
        $this->assertTrue($this->app->removeSwimlane(1, 2));
    }

    public function testChangePosition()
    {
        $this->assertNotFalse($this->app->addSwimlane(1, 'Swimlane 1'));
        $this->assertNotFalse($this->app->addSwimlane(1, 'Swimlane 2'));

        $swimlanes = $this->app->getAllSwimlanes(1);
        $this->assertCount(3, $swimlanes);

        $this->assertTrue($this->app->changeSwimlanePosition(1, 1, 3));
        $this->assertFalse($this->app->changeSwimlanePosition(1, 1, 6));
    }
}
