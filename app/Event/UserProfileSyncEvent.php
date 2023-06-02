<?php

namespace Kanboard\Event;

use Kanboard\Core\User\UserProviderInterface;
use Kanboard\User\LdapUserProvider;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class UserProfileSyncEvent
 *
 * @package Kanboard\Event
 * @author  Fredic Guillot
 */
class UserProfileSyncEvent extends Event
{
    /**
     * User profile
     *
     * @var array
     */
    private $profile;

    /**
     * User provider
     *
     * @var UserProviderInterface
     */
    private $user;

    /**
     * UserProfileSyncEvent constructor.
     *
     * @param array                 $profile
     * @param UserProviderInterface $user
     */
    public function __construct(array $profile, UserProviderInterface $user)
    {
        $this->profile = $profile;
        $this->user = $user;
    }

    /**
     * Get user profile
     *
     * @access public
     * @return array
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Get user provider object
     *
     * @access public
     * @return UserProviderInterface|LdapUserProvider
     */
    public function getUser()
    {
        return $this->user;
    }
}
