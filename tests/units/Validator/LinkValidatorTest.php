<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\LinkValidator;

class LinkValidatorTest extends Base
{
    public function testValidateCreation()
    {
        $linkValidator = new LinkValidator($this->container);

        $r = $linkValidator->validateCreation(array('label' => 'a'));
        $this->assertTrue($r[0]);

        $r = $linkValidator->validateCreation(array('label' => 'a', 'opposite_label' => 'b'));
        $this->assertTrue($r[0]);

        $r = $linkValidator->validateCreation(array('label' => 'relates to'));
        $this->assertFalse($r[0]);

        $r = $linkValidator->validateCreation(array('label' => 'a', 'opposite_label' => 'a'));
        $this->assertFalse($r[0]);

        $r = $linkValidator->validateCreation(array('label' => ''));
        $this->assertFalse($r[0]);
    }

    public function testValidateModification()
    {
        $validator = new LinkValidator($this->container);

        $r = $validator->validateModification(array('id' => 20, 'label' => 'a', 'opposite_id' => 0));
        $this->assertTrue($r[0]);

        $r = $validator->validateModification(array('id' => 20, 'label' => 'a', 'opposite_id' => '1'));
        $this->assertTrue($r[0]);

        $r = $validator->validateModification(array('id' => 20, 'label' => 'relates to', 'opposite_id' => '1'));
        $this->assertFalse($r[0]);

        $r = $validator->validateModification(array('id' => 20, 'label' => '', 'opposite_id' => '1'));
        $this->assertFalse($r[0]);

        $r = $validator->validateModification(array('label' => '', 'opposite_id' => '1'));
        $this->assertFalse($r[0]);

        $r = $validator->validateModification(array('id' => 20, 'opposite_id' => '1'));
        $this->assertFalse($r[0]);

        $r = $validator->validateModification(array('label' => 'test'));
        $this->assertFalse($r[0]);
    }
}
