<?php

use Kanboard\Core\Security\Role;
use Kanboard\Event\UserProfileSyncEvent;
use Kanboard\Model\UserModel;
use Kanboard\Subscriber\LdapUserPhotoSubscriber;
use Kanboard\User\DatabaseUserProvider;
use Kanboard\User\LdapUserProvider;

require_once __DIR__.'/../Base.php';

class LdapUserPhotoSubscriberTest extends Base
{
    public function testWhenTheProviderIsNotLdap()
    {
        $userProvider = new DatabaseUserProvider(array());
        $subscriber = new LdapUserPhotoSubscriber($this->container);
        $userModel = new UserModel($this->container);

        $userModel->update(array('id' => 1, 'avatar_path' => 'my avatar'));
        $user = $userModel->getById(1);

        $subscriber->syncUserPhoto(new UserProfileSyncEvent($user, $userProvider));

        $user = $userModel->getById(1);
        $this->assertEquals('my avatar', $user['avatar_path']);
    }

    public function testWhenTheUserHaveLdapPhoto()
    {
        $userProvider = new LdapUserProvider('dn', 'admin', 'Admin', 'admin@localhost', Role::APP_ADMIN, array(), 'my photo');
        $subscriber = new LdapUserPhotoSubscriber($this->container);
        $userModel = new UserModel($this->container);
        $user = $userModel->getById(1);

        $this->container['objectStorage']
            ->expects($this->once())
            ->method('put')
            ->with($this->anything(), 'my photo');


        $subscriber->syncUserPhoto(new UserProfileSyncEvent($user, $userProvider));

        $user = $userModel->getById(1);
        $this->assertStringStartsWith('avatars', $user['avatar_path']);
    }

    public function testWhenTheUserDoNotHaveLdapPhoto()
    {
        $userProvider = new LdapUserProvider('dn', 'admin', 'Admin', 'admin@localhost', Role::APP_ADMIN, array());
        $subscriber = new LdapUserPhotoSubscriber($this->container);
        $userModel = new UserModel($this->container);
        $user = $userModel->getById(1);

        $this->container['objectStorage']
            ->expects($this->never())
            ->method('put');

        $subscriber->syncUserPhoto(new UserProfileSyncEvent($user, $userProvider));

        $user = $userModel->getById(1);
        $this->assertEmpty($user['avatar_path']);
    }

    public function testWhenTheUserAlreadyHaveAvatar()
    {
        $userProvider = new LdapUserProvider('dn', 'admin', 'Admin', 'admin@localhost', Role::APP_ADMIN, array(), 'my photo');
        $subscriber = new LdapUserPhotoSubscriber($this->container);
        $userModel = new UserModel($this->container);

        $userModel->update(array('id' => 1, 'avatar_path' => 'my avatar'));
        $user = $userModel->getById(1);

        $this->container['objectStorage']
            ->expects($this->never())
            ->method('put');

        $subscriber->syncUserPhoto(new UserProfileSyncEvent($user, $userProvider));

        $user = $userModel->getById(1);
        $this->assertEquals('my avatar', $user['avatar_path']);
    }
}
