<?php

namespace Kanboard\User;

/**
 * Google OAuth User Provider
 *
 * @package  user
 * @author   Frederic Guillot
 */
class GoogleUserProvider extends OAuthUserProvider
{
    /**
     * Get external id column name
     *
     * @access public
     * @return string
     */
    public function getExternalIdColumn()
    {
        return 'google_id';
    }
}
