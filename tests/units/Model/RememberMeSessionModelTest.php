<?php

namespace KanboardTests\units\Model;

use KanboardTests\units\Base;
use Kanboard\Model\RememberMeSessionModel;
use Kanboard\Model\UserModel;

class RememberMeSessionModelTest extends Base
{
    public function testRemoveAll()
    {
        $userModel = new UserModel($this->container);
        $rememberMeSessionModel = new RememberMeSessionModel($this->container);

        $this->assertEquals(2, $userModel->create(array('username' => 'user1')));

        $rememberMeSessionModel->create(1, '127.0.0.1', 'Firefox');
        $rememberMeSessionModel->create(1, '127.0.0.1', 'Chrome');
        $rememberMeSessionModel->create(2, '127.0.0.1', 'Firefox');

        $this->assertCount(2, $rememberMeSessionModel->getAll(1));
        $this->assertCount(1, $rememberMeSessionModel->getAll(2));

        $this->assertTrue($rememberMeSessionModel->removeAll(1));

        $this->assertCount(0, $rememberMeSessionModel->getAll(1));
        $this->assertCount(1, $rememberMeSessionModel->getAll(2));
    }

    public function testRemoveAllOnPasswordChange()
    {
        $userModel = new UserModel($this->container);
        $rememberMeSessionModel = new RememberMeSessionModel($this->container);

        $rememberMeSessionModel->create(1, '127.0.0.1', 'Firefox');
        $this->assertCount(1, $rememberMeSessionModel->getAll(1));

        // Updating something else keeps the persistent sessions
        $this->assertTrue($userModel->update(array('id' => 1, 'name' => 'Bob')));
        $this->assertCount(1, $rememberMeSessionModel->getAll(1));

        $this->assertTrue($userModel->update(array('id' => 1, 'password' => 'new_password')));
        $this->assertCount(0, $rememberMeSessionModel->getAll(1));
    }
}
