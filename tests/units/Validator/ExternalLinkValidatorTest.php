<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Validator\ExternalLinkValidator;

class ExternalLinkValidatorTest extends Base
{
    public function testValidateCreation()
    {
        $externalLinkValidator = new ExternalLinkValidator($this->container);

        $result = $externalLinkValidator->validateCreation(array('url' => 'http://somewhere', 'task_id' => 1, 'title' => 'Title', 'link_type' => 'weblink', 'dependency' => 'related'));
        $this->assertTrue($result[0]);

        $result = $externalLinkValidator->validateCreation(array('url' => 'http://somewhere', 'task_id' => 'abc', 'title' => 'Title', 'link_type' => 'weblink', 'dependency' => 'related'));
        $this->assertFalse($result[0]);

        $result = $externalLinkValidator->validateCreation(array('url' => 'http://somewhere', 'task_id' => 1, 'title' => 'Title', 'link_type' => 'weblink'));
        $this->assertFalse($result[0]);

        $result = $externalLinkValidator->validateCreation(array('url' => 'http://somewhere', 'task_id' => 1, 'title' => 'Title', 'dependency' => 'related'));
        $this->assertFalse($result[0]);

        $result = $externalLinkValidator->validateCreation(array('url' => 'http://somewhere', 'task_id' => 1, 'link_type' => 'weblink', 'dependency' => 'related'));
        $this->assertFalse($result[0]);

        $result = $externalLinkValidator->validateCreation(array('url' => 'http://somewhere', 'title' => 'Title', 'link_type' => 'weblink', 'dependency' => 'related'));
        $this->assertFalse($result[0]);

        $result = $externalLinkValidator->validateCreation(array('task_id' => 1, 'title' => 'Title', 'link_type' => 'weblink', 'dependency' => 'related'));
        $this->assertFalse($result[0]);
    }

    public function testValidateModification()
    {
        $validator = new ExternalLinkValidator($this->container);

        $result = $validator->validateModification(array('id' => 1, 'url' => 'http://somewhere', 'task_id' => 1, 'title' => 'Title', 'link_type' => 'weblink', 'dependency' => 'related'));
        $this->assertTrue($result[0]);

        $result = $validator->validateModification(array('id' => 1, 'url' => 'http://somewhere', 'task_id' => 'abc', 'title' => 'Title', 'link_type' => 'weblink', 'dependency' => 'related'));
        $this->assertFalse($result[0]);

        $result = $validator->validateModification(array('id' => 1, 'url' => 'http://somewhere', 'task_id' => 1, 'title' => 'Title', 'link_type' => 'weblink'));
        $this->assertFalse($result[0]);

        $result = $validator->validateModification(array('id' => 1, 'url' => 'http://somewhere', 'task_id' => 1, 'title' => 'Title', 'dependency' => 'related'));
        $this->assertFalse($result[0]);

        $result = $validator->validateModification(array('id' => 1, 'url' => 'http://somewhere', 'task_id' => 1, 'link_type' => 'weblink', 'dependency' => 'related'));
        $this->assertFalse($result[0]);

        $result = $validator->validateModification(array('id' => 1, 'url' => 'http://somewhere', 'title' => 'Title', 'link_type' => 'weblink', 'dependency' => 'related'));
        $this->assertFalse($result[0]);

        $result = $validator->validateModification(array('id' => 1, 'task_id' => 1, 'title' => 'Title', 'link_type' => 'weblink', 'dependency' => 'related'));
        $this->assertFalse($result[0]);

        $result = $validator->validateModification(array('url' => 'http://somewhere', 'task_id' => 1, 'title' => 'Title', 'link_type' => 'weblink', 'dependency' => 'related'));
        $this->assertFalse($result[0]);
    }
}
