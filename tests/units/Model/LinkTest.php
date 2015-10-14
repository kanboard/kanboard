<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Link;

class LinkTest extends Base
{
    public function testCreateLink()
    {
        $l = new Link($this->container);

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
        $l = new Link($this->container);

        $this->assertNotFalse($l->create('Link A'));
        $this->assertNotFalse($l->create('Link B', 'Link C'));

        $this->assertEquals(1, $l->getOppositeLinkId(1));
        $this->assertEquals(3, $l->getOppositeLinkId(2));
        $this->assertEquals(2, $l->getOppositeLinkId(3));
    }

    public function testUpdate()
    {
        $l = new Link($this->container);

        $this->assertTrue($l->update(array('id' => 2, 'label' => 'test', 'opposite_id' => 0)));

        $link = $l->getById(2);
        $this->assertNotEmpty($link);
        $this->assertEquals('test', $link['label']);
        $this->assertEquals(0, $link['opposite_id']);
    }

    public function testRemove()
    {
        $l = new Link($this->container);

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
        $l = new Link($this->container);
        $links = $l->getMergedList();

        $this->assertNotEmpty($links);
        $this->assertCount(11, $links);
        $this->assertEquals('blocks', $links[1]['label']);
        $this->assertEquals('is blocked by', $links[1]['opposite_label']);
    }

    public function testGetList()
    {
        $l = new Link($this->container);
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

    public function testValidateCreation()
    {
        $l = new Link($this->container);

        $r = $l->validateCreation(array('label' => 'a'));
        $this->assertTrue($r[0]);

        $r = $l->validateCreation(array('label' => 'a', 'opposite_label' => 'b'));
        $this->assertTrue($r[0]);

        $r = $l->validateCreation(array('label' => 'relates to'));
        $this->assertFalse($r[0]);

        $r = $l->validateCreation(array('label' => 'a', 'opposite_label' => 'a'));
        $this->assertFalse($r[0]);

        $r = $l->validateCreation(array('label' => ''));
        $this->assertFalse($r[0]);
    }

    public function testValidateModification()
    {
        $l = new Link($this->container);

        $r = $l->validateModification(array('id' => 20, 'label' => 'a', 'opposite_id' => 0));
        $this->assertTrue($r[0]);

        $r = $l->validateModification(array('id' => 20, 'label' => 'a', 'opposite_id' => '1'));
        $this->assertTrue($r[0]);

        $r = $l->validateModification(array('id' => 20, 'label' => 'relates to', 'opposite_id' => '1'));
        $this->assertFalse($r[0]);

        $r = $l->validateModification(array('id' => 20, 'label' => '', 'opposite_id' => '1'));
        $this->assertFalse($r[0]);

        $r = $l->validateModification(array('label' => '', 'opposite_id' => '1'));
        $this->assertFalse($r[0]);

        $r = $l->validateModification(array('id' => 20, 'opposite_id' => '1'));
        $this->assertFalse($r[0]);

        $r = $l->validateModification(array('label' => 'test'));
        $this->assertFalse($r[0]);
    }
}
