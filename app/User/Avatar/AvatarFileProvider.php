<?php

namespace Kanboard\User\Avatar;

use Kanboard\Core\Base;
use Kanboard\Core\User\Avatar\AvatarProviderInterface;

/**
 * Avatar Local Image File Provider
 *
 * @package  avatar
 * @author   Frederic Guillot
 */
class AvatarFileProvider extends Base implements AvatarProviderInterface
{
    /**
     * Render avatar html
     *
     * @access public
     * @param  array $user
     * @param  int   $size
     * @return string
     */
    public function render(array $user, $size)
    {
        $url = $this->helper->url->href('AvatarFileController', 'image', array('user_id' => $user['id'], 'size' => $size));
        $title = $this->helper->text->e($user['name'] ?: $user['username']);
        return '<img src="' . $url . '" alt="' . $title . '" title="' . $title . '">';
    }

    /**
     * Determine if the provider is active
     *
     * @access public
     * @param  array $user
     * @return boolean
     */
    public function isActive(array $user)
    {
        return !empty($user['avatar_path']);
    }
}
