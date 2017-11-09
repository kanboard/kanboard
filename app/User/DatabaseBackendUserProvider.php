<?php

namespace Kanboard\User;

use Kanboard\Core\Base;
use Kanboard\Core\User\UserBackendProviderInterface;
use Kanboard\Filter\UserNameFilter;
use Kanboard\Model\UserModel;

/**
 * Database Backend User Provider
 *
 * @package  Kanboard\User
 * @author   Frederic Guillot
 */
class DatabaseBackendUserProvider extends Base implements UserBackendProviderInterface
{
    /**
     * Find a group from a search query
     *
     * @access public
     * @param  string $input
     * @return DatabaseUserProvider[]
     */
    public function find($input)
    {
        $result = array();

        $users = $this->userQuery->withFilter(new UserNameFilter($input))
            ->getQuery()
            ->columns(UserModel::TABLE.'.id', UserModel::TABLE.'.username', UserModel::TABLE.'.name')
            ->eq(UserModel::TABLE.'.is_active', 1)
            ->asc(UserModel::TABLE.'.name')
            ->asc(UserModel::TABLE.'.username')
            ->findAll();

        foreach ($users as $user) {
            $result[] = new DatabaseUserProvider($user);
        }

        return $result;
    }
}
