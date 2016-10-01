<?php

namespace Kanboard\Core\User;

use Kanboard\Core\Base;
use Kanboard\Event\UserProfileSyncEvent;

/**
 * User Profile
 *
 * @package  user
 * @author   Frederic Guillot
 */
class UserProfile extends Base
{
    const EVENT_USER_PROFILE_AFTER_SYNC = 'user_profile.after.sync';

    /**
     * Assign provider data to the local user
     *
     * @access public
     * @param  integer                $userId
     * @param  UserProviderInterface  $user
     * @return boolean
     */
    public function assign($userId, UserProviderInterface $user)
    {
        $profile = $this->userModel->getById($userId);

        $values = UserProperty::filterProperties($profile, UserProperty::getProperties($user));
        $values['id'] = $userId;

        if ($this->userModel->update($values)) {
            $profile = array_merge($profile, $values);
            $this->userSession->initialize($profile);
            return true;
        }

        return false;
    }

    /**
     * Synchronize user properties with the local database and create the user session
     *
     * @access public
     * @param  UserProviderInterface $user
     * @return boolean
     */
    public function initialize(UserProviderInterface $user)
    {
        if ($user->getInternalId()) {
            $profile = $this->userModel->getById($user->getInternalId());
        } elseif ($user->getExternalIdColumn() && $user->getExternalId()) {
            $profile = $this->userSync->synchronize($user);
            $this->groupSync->synchronize($profile['id'], $user->getExternalGroupIds());
        }

        if (! empty($profile) && $profile['is_active'] == 1) {
            $this->userSession->initialize($profile);
            $this->dispatcher->dispatch(self::EVENT_USER_PROFILE_AFTER_SYNC, new UserProfileSyncEvent($profile, $user));
            return true;
        }

        return false;
    }
}
