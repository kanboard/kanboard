<?php

namespace Kanboard\User\Avatar;

use Kanboard\Core\Base;
use Kanboard\Core\User\Avatar\AvatarProviderInterface;

/**
 * Gravatar Avatar Provider
 *
 * @package  avatar
 * @author   Frederic Guillot
 */
class GravatarProvider extends Base implements AvatarProviderInterface
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
        $url = sprintf('https://www.gravatar.com/avatar/%s?s=%d', md5(strtolower($user['email'])), $size);
        $title = $this->helper->text->e($user['name'] ?: $user['username']);
        return '<img src="'.$url.'" alt="'.$title.'" title="'.$title.'">';
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
        return !empty($user['email']) && $this->config->get('integration_gravatar') == 1;
    }
}
