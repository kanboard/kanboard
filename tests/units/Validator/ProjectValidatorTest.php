<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\ProjectValidator;
use Kanboard\Model\Project;

class ProjectValidatorTest extends Base
{
    public function testValidateCreation()
    {
        $validator = new ProjectValidator($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1', 'identifier' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2')));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals('TEST1', $project['identifier']);

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('', $project['identifier']);

        $r = $validator->validateCreation(array('name' => 'test', 'identifier' => 'TEST1'));
        $this->assertFalse($r[0]);

        $r = $validator->validateCreation(array('name' => 'test', 'identifier' => 'test1'));
        $this->assertFalse($r[0]);

        $r = $validator->validateCreation(array('name' => 'test', 'identifier' => 'a-b-c'));
        $this->assertFalse($r[0]);

        $r = $validator->validateCreation(array('name' => 'test', 'identifier' => 'test 123'));
        $this->assertFalse($r[0]);
    }

    public function testValidateModification()
    {
        $validator = new ProjectValidator($this->container);
        $p = new Project($this->container);

        $this->assertEquals(1, $p->create(array('name' => 'UnitTest1', 'identifier' => 'test1')));
        $this->assertEquals(2, $p->create(array('name' => 'UnitTest2', 'identifier' => 'TEST2')));

        $project = $p->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals('TEST1', $project['identifier']);

        $project = $p->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('TEST2', $project['identifier']);

        $r = $validator->validateModification(array('id' => 1, 'name' => 'test', 'identifier' => 'TEST1'));
        $this->assertTrue($r[0]);

        $r = $validator->validateModification(array('id' => 1, 'name' => 'test', 'identifier' => 'test3'));
        $this->assertTrue($r[0]);

        $r = $validator->validateModification(array('id' => 1, 'name' => 'test', 'identifier' => ''));
        $this->assertTrue($r[0]);

        $r = $validator->validateModification(array('id' => 1, 'name' => 'test', 'identifier' => 'TEST2'));
        $this->assertFalse($r[0]);
    }
}
