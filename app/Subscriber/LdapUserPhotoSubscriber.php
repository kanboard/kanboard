<?php

namespace Kanboard\Subscriber;

use Kanboard\Core\User\UserProfile;
use Kanboard\Event\UserProfileSyncEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LdapUserPhotoSubscriber
 *
 * @package Kanboard\Subscriber
 * @author  Frederic Guillot
 */
class LdapUserPhotoSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    /**
     * Get event listeners
     *
     * @static
     * @access public
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            UserProfile::EVENT_USER_PROFILE_AFTER_SYNC => 'syncUserPhoto',
        );
    }

    /**
     * Save the user profile photo from LDAP to the object storage
     *
     * @access public
     * @param  UserProfileSyncEvent $event
     */
    public function syncUserPhoto(UserProfileSyncEvent $event)
    {
        if (is_a($event->getUser(), 'Kanboard\User\LdapUserProvider')) {
            $profile = $event->getProfile();
            $photo = $event->getUser()->getPhoto();

            if (empty($profile['avatar_path']) && ! empty($photo)) {
                $this->logger->info('Saving user photo from LDAP profile');
                $this->avatarFileModel->uploadImageContent($profile['id'], $photo);
            }
        }
    }
}
