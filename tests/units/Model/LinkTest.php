<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\LinkModel;

class LinkTest extends Base
{
    public function testCreateLink()
    {
        $l = new LinkModel($this->container);

        $this->assertNotFalse($l->create('Link A'));
        $this->assertFalse($l->create('Link A'));
        $this->assertNotFalse($l->create('Link B', 'Link C'));

        $links = $l->getAll();
        $this->assertNotEmpty($links);
        $this->assertCount(14, $links);

        $link = $l->getByLabel('Link A');
        $this->assertNotEmpty($link);
        $this->assertEquals('Link A', $link['label']);
        $this->assertEquals(0, $link['opposite_id']);

        $link1 = $l->getByLabel('Link B');
        $this->assertNotEmpty($link1);
        $this->assertEquals('Link B', $link1['label']);
        $this->assertNotEmpty($link1['opposite_id']);

        $link2 = $l->getByLabel('Link C');
        $this->assertNotEmpty($link2);
        $this->assertEquals('Link C', $link2['label']);
        $this->assertNotEmpty($link2['opposite_id']);

        $this->assertNotEquals($link1['opposite_id'], $link2['opposite_id']);
    }

    public function testGetOppositeLinkId()
    {
        $l = new LinkModel($this->container);

        $this->assertNotFalse($l->create('Link A'));
        $this->assertNotFalse($l->create('Link B', 'Link C'));

        $this->assertEquals(1, $l->getOppositeLinkId(1));
        $this->assertEquals(3, $l->getOppositeLinkId(2));
        $this->assertEquals(2, $l->getOppositeLinkId(3));
    }

    public function testUpdate()
    {
        $l = new LinkModel($this->container);

        $this->assertTrue($l->update(array('id' => 2, 'label' => 'test', 'opposite_id' => 0)));

        $link = $l->getById(2);
        $this->assertNotEmpty($link);
        $this->assertEquals('test', $link['label']);
        $this->assertEquals(0, $link['opposite_id']);
    }

    public function testRemove()
    {
        $l = new LinkModel($this->container);

        $link = $l->getById(3);
        $this->assertNotEmpty($link);
        $this->assertEquals('is blocked by', $link['label']);
        $this->assertEquals(2, $link['opposite_id']);

        $this->assertTrue($l->remove(2));

        $link = $l->getById(2);
        $this->assertEmpty($link);

        $link = $l->getById(3);
        $this->assertNotEmpty($link);
        $this->assertEquals('is blocked by', $link['label']);
        $this->assertEquals(0, $link['opposite_id']);
    }

    public function testGetMergedList()
    {
        $l = new LinkModel($this->container);
        $links = $l->getMergedList();

        $this->assertNotEmpty($links);
        $this->assertCount(11, $links);
        $this->assertEquals('blocks', $links[1]['label']);
        $this->assertEquals('is blocked by', $links[1]['opposite_label']);
    }

    public function testGetList()
    {
        $l = new LinkModel($this->container);
        $links = $l->getList();

        $this->assertNotEmpty($links);
        $this->assertCount(12, $links);
        $this->assertEquals('', $links[0]);
        $this->assertEquals('relates to', $links[1]);

        $links = $l->getList(1);

        $this->assertNotEmpty($links);
        $this->assertCount(11, $links);
        $this->assertEquals('', $links[0]);
        $this->assertArrayNotHasKey(1, $links);
        $this->assertEquals('blocks', $links[2]);

        $links = $l->getList(1, false);

        $this->assertNotEmpty($links);
        $this->assertCount(10, $links);
        $this->assertArrayNotHasKey(0, $links);
        $this->assertArrayNotHasKey(1, $links);
        $this->assertEquals('blocks', $links[2]);

        $links = $l->getList(0, false);

        $this->assertNotEmpty($links);
        $this->assertCount(11, $links);
        $this->assertArrayNotHasKey(0, $links);
        $this->assertEquals('relates to', $links[1]);
    }
}
