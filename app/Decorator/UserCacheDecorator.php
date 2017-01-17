<?php

namespace Kanboard\Decorator;

use Kanboard\Core\Cache\CacheInterface;
use Kanboard\Model\UserModel;

/**
 * Class UserCacheDecorator
 *
 * @package Kanboard\Decorator
 * @author  Frederic Guillot
 */
class UserCacheDecorator
{
    protected $cachePrefix = 'user_model:';

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * UserCacheDecorator constructor.
     *
     * @param CacheInterface $cache
     * @param UserModel      $userModel
     */
    public function __construct(CacheInterface $cache, UserModel $userModel)
    {
        $this->cache = $cache;
        $this->userModel = $userModel;
    }

    /**
     * Get a specific user by the username
     *
     * @access public
     * @param  string  $username  Username
     * @return array
     */
    public function getByUsername($username)
    {
        $key = $this->cachePrefix.$username;
        $user = $this->cache->get($key);

        if ($user === null) {
            $user = $this->userModel->getByUsername($username);
            $this->cache->set($key, $user);
        }

        return $user;
    }
}
