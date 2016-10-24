<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\ProjectValidator;
use Kanboard\Model\ProjectModel;

class ProjectValidatorTest extends Base
{
    public function testValidateCreation()
    {
        $projectValidator = new ProjectValidator($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest1', 'identifier' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'UnitTest2')));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals('TEST1', $project['identifier']);

        $project = $projectModel->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('', $project['identifier']);

        $r = $projectValidator->validateCreation(array('name' => 'test', 'identifier' => 'TEST1'));
        $this->assertFalse($r[0]);

        $r = $projectValidator->validateCreation(array('name' => 'test', 'identifier' => 'test1'));
        $this->assertFalse($r[0]);

        $r = $projectValidator->validateCreation(array('name' => 'test', 'identifier' => 'a-b-c'));
        $this->assertFalse($r[0]);

        $r = $projectValidator->validateCreation(array('name' => 'test', 'identifier' => 'test 123'));
        $this->assertFalse($r[0]);
    }

    public function testValidateModification()
    {
        $projectValidator = new ProjectValidator($this->container);
        $projectModel = new ProjectModel($this->container);

        $this->assertEquals(1, $projectModel->create(array('name' => 'UnitTest1', 'identifier' => 'test1')));
        $this->assertEquals(2, $projectModel->create(array('name' => 'UnitTest2', 'identifier' => 'TEST2')));

        $project = $projectModel->getById(1);
        $this->assertNotEmpty($project);
        $this->assertEquals('TEST1', $project['identifier']);

        $project = $projectModel->getById(2);
        $this->assertNotEmpty($project);
        $this->assertEquals('TEST2', $project['identifier']);

        $r = $projectValidator->validateModification(array('id' => 1, 'name' => 'test', 'identifier' => 'TEST1'));
        $this->assertTrue($r[0]);

        $r = $projectValidator->validateModification(array('id' => 1, 'identifier' => 'test3'));
        $this->assertTrue($r[0]);

        $r = $projectValidator->validateModification(array('id' => 1, 'identifier' => ''));
        $this->assertTrue($r[0]);

        $r = $projectValidator->validateModification(array('id' => 1, 'identifier' => 'TEST2'));
        $this->assertFalse($r[0]);

        $r = $projectValidator->validateModification(array('id' => 1, 'name' => ''));
        $this->assertFalse($r[0]);

        $r = $projectValidator->validateModification(array('id' => 1, 'name' => null));
        $this->assertFalse($r[0]);
    }
}
