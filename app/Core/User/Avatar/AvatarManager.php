<?php

namespace Kanboard\Core\User\Avatar;

/**
 * Avatar Manager
 *
 * @package  avatar
 * @author   Frederic Guillot
 */
class AvatarManager
{
    /**
     * Providers
     *
     * @access private
     * @var AvatarProviderInterface[]
     */
    private $providers = array();

    /**
     * Register a new Avatar provider
     *
     * @access public
     * @param  AvatarProviderInterface $provider
     * @return $this
     */
    public function register(AvatarProviderInterface $provider)
    {
        $this->providers[] = $provider;
        return $this;
    }

    /**
     * Render avatar HTML element
     *
     * @access public
     * @param  string   $user_id
     * @param  string   $username
     * @param  string   $name
     * @param  string   $email
     * @param  string   $avatar_path
     * @param  int      $size
     * @return string
     */
    public function render($user_id, $username, $name, $email, $avatar_path, $size)
    {
        $user = array(
            'id' => $user_id,
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'avatar_path' => $avatar_path,
        );

        krsort($this->providers);

        foreach ($this->providers as $provider) {
            if ($provider->isActive($user)) {
                return $provider->render($user, $size);
            }
        }

        return '';
    }

    /**
     * Render default provider for unknown users (first provider registered)
     *
     * @access public
     * @param  integer $size
     * @return string
     */
    public function renderDefault($size)
    {
        if (count($this->providers) > 0) {
            ksort($this->providers);
            $provider = current($this->providers);

            $user = array(
                'id' => 0,
                'username' => '',
                'name' => '?',
                'email' => '',
                'avatar_path' => '',
            );

            return $provider->render($user, $size);
        }

        return '';
    }
}
