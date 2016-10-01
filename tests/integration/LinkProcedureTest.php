<?php

require_once __DIR__.'/BaseProcedureTest.php';

class LinkProcedureTest extends BaseProcedureTest
{
    public function testGetAllLinks()
    {
        $links = $this->app->getAllLinks();
        $this->assertNotEmpty($links);
        $this->assertArrayHasKey('id', $links[0]);
        $this->assertArrayHasKey('label', $links[0]);
        $this->assertArrayHasKey('opposite_id', $links[0]);
    }

    public function testGetOppositeLink()
    {
        $link = $this->app->getOppositeLinkId(1);
        $this->assertEquals(1, $link);

        $link = $this->app->getOppositeLinkId(2);
        $this->assertEquals(3, $link);
    }

    public function testGetLinkByLabel()
    {
        $link = $this->app->getLinkByLabel('blocks');
        $this->assertNotEmpty($link);
        $this->assertEquals(2, $link['id']);
        $this->assertEquals(3, $link['opposite_id']);
    }

    public function testGetLinkById()
    {
        $link = $this->app->getLinkById(4);
        $this->assertNotEmpty($link);
        $this->assertEquals(4, $link['id']);
        $this->assertEquals(5, $link['opposite_id']);
        $this->assertEquals('duplicates', $link['label']);
    }

    public function testCreateLink()
    {
        $link_id = $this->app->createLink(array('label' => 'test'));
        $this->assertNotFalse($link_id);
        $this->assertInternalType('int', $link_id);

        $link_id = $this->app->createLink(array('label' => 'foo', 'opposite_label' => 'bar'));
        $this->assertNotFalse($link_id);
        $this->assertInternalType('int', $link_id);
    }

    public function testUpdateLink()
    {
        $link1 = $this->app->getLinkByLabel('bar');
        $this->assertNotEmpty($link1);

        $link2 = $this->app->getLinkByLabel('test');
        $this->assertNotEmpty($link2);

        $this->assertNotFalse($this->app->updateLink($link1['id'], $link2['id'], 'my link'));

        $link = $this->app->getLinkById($link1['id']);
        $this->assertNotEmpty($link);
        $this->assertEquals($link2['id'], $link['opposite_id']);
        $this->assertEquals('my link', $link['label']);

        $this->assertTrue($this->app->removeLink($link1['id']));
    }
}
