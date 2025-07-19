<?php

namespace Kanboard\Core\User;

use Kanboard\Core\Base;
use Kanboard\Notification\MailNotification;
use Kanboard\Notification\WebNotification;

/**
 * User Synchronization
 *
 * @package  user
 * @author   Frederic Guillot
 */
class UserSync extends Base
{
    /**
     * Synchronize user profile
     *
     * @access public
     * @param  UserProviderInterface $user
     * @return array|null
     */
    public function synchronize(UserProviderInterface $user)
    {
        $profile = $this->userModel->getByExternalId($user->getExternalIdColumn(), $user->getExternalId());
        $properties = UserProperty::getProperties($user);

        if (! empty($profile)) {
            $profile = $this->updateUser($profile, $properties);
        } elseif ($user->isUserCreationAllowed()) {
            $profile = $this->createUser($user, $properties);
        }

        return $profile;
    }

    /**
     * Update user profile
     *
     * @access public
     * @param  array    $profile
     * @param  array    $properties
     * @return array
     */
    private function updateUser(array $profile, array $properties)
    {
        $values = UserProperty::filterProperties($profile, $properties);

        if (! empty($values)) {
            $values['id'] = $profile['id'];
            $result = $this->userModel->update($values);
            return $result ? array_merge($profile, $properties) : $profile;
        }

        return $profile;
    }

    /**
     * Create user
     *
     * @access public
     * @param  UserProviderInterface  $user
     * @param  array                  $properties
     * @return array
     */
    private function createUser(UserProviderInterface $user, array $properties)
    {
        $userId = $this->userModel->create($properties);

        if ($userId === false) {
            $this->logger->error('Unable to create user profile: '.$user->getExternalId());
            return array();
        }

        if ($this->configModel->get('notifications_enabled', 0) == 1) {
            $this->userNotificationTypeModel->saveSelectedTypes($userId, [MailNotification::TYPE, WebNotification::TYPE]);
        }

        return $this->userModel->getById($userId);
    }
}
