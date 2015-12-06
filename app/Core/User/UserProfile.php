<?php

namespace Kanboard\Core\User;

use Kanboard\Core\Base;

/**
 * User Profile
 *
 * @package  user
 * @author   Frederic Guillot
 */
class UserProfile extends Base
{
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
        $profile = $this->user->getById($userId);

        $values = UserProperty::filterProperties($profile, UserProperty::getProperties($user));
        $values['id'] = $userId;

        if ($this->user->update($values)) {
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
            $profile = $this->user->getById($user->getInternalId());
        } elseif ($user->getExternalIdColumn() && $user->getExternalId()) {
            $profile = $this->userSync->synchronize($user);
            $this->groupSync->synchronize($profile['id'], $user->getExternalGroupIds());
        }

        if (! empty($profile)) {
            $this->userSession->initialize($profile);
            return true;
        }

        return false;
    }
}
