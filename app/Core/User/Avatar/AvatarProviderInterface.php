<?php

namespace Kanboard\Core\User\Avatar;

/**
 * Avatar Provider Interface
 *
 * @package  user
 * @author   Frederic Guillot
 */
interface AvatarProviderInterface
{
    /**
     * Render avatar html
     *
     * @access public
     * @param  array $user
     * @param  int   $size
     */
    public function render(array $user, $size);

    /**
     * Determine if the provider is active
     *
     * @access public
     * @param  array $user
     * @return boolean
     */
    public function isActive(array $user);
}
