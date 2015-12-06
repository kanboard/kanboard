<?php

namespace Kanboard\User;

/**
 * Github OAuth User Provider
 *
 * @package  user
 * @author   Frederic Guillot
 */
class GithubUserProvider extends OAuthUserProvider
{
    /**
     * Get external id column name
     *
     * @access public
     * @return string
     */
    public function getExternalIdColumn()
    {
        return 'github_id';
    }
}
