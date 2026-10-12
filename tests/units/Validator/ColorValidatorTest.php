<?php

namespace KanboardTests\units\Validator;

use Kanboard\Validator\CategoryValidator;
use Kanboard\Validator\TagValidator;
use Kanboard\Validator\TaskValidator;
use KanboardTests\units\Base;

class ColorValidatorTest extends Base
{
    public function testTaskColorMustBeRegistered()
    {
        $validator = new TaskValidator($this->container);

        $this->assertTrue($validator->validateCreation(array('project_id' => 1, 'title' => 'Task', 'color_id' => 'blue'))[0]);
        $this->assertFalse($validator->validateCreation(array('project_id' => 1, 'title' => 'Task', 'color_id' => 'blue"><style>'))[0]);
        $this->assertFalse($validator->validateModification(array('id' => 1, 'title' => 'Task', 'color_id' => 'blue"><style>'))[0]);
        $this->assertFalse($validator->validateApiModification(array('id' => 1, 'color_id' => 'blue"><style>'))[0]);
    }

    public function testTagColorMustBeRegistered()
    {
        $validator = new TagValidator($this->container);

        $this->assertTrue($validator->validateCreation(array('project_id' => 1, 'name' => 'Tag', 'color_id' => ''))[0]);
        $this->assertTrue($validator->validateCreation(array('project_id' => 1, 'name' => 'Tag', 'color_id' => 'blue'))[0]);
        $this->assertFalse($validator->validateCreation(array('project_id' => 1, 'name' => 'Tag', 'color_id' => 'blue"><style>'))[0]);
        $this->assertFalse($validator->validateModification(array('id' => 1, 'project_id' => 1, 'name' => 'Tag', 'color_id' => 'blue"><style>'))[0]);
    }

    public function testCategoryColorMustBeRegistered()
    {
        $validator = new CategoryValidator($this->container);

        $this->assertTrue($validator->validateCreation(array('project_id' => 1, 'name' => 'Category', 'color_id' => ''))[0]);
        $this->assertTrue($validator->validateCreation(array('project_id' => 1, 'name' => 'Category', 'color_id' => 'blue'))[0]);
        $this->assertFalse($validator->validateCreation(array('project_id' => 1, 'name' => 'Category', 'color_id' => 'blue"><style>'))[0]);
        $this->assertFalse($validator->validateModification(array('id' => 1, 'project_id' => 1, 'name' => 'Category', 'color_id' => 'blue"><style>'))[0]);
    }
}
