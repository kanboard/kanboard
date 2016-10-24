<?php

namespace Kanboard\Decorator;

use Kanboard\Core\Cache\CacheInterface;
use Kanboard\Model\ProjectRoleRestrictionModel;

/**
 * Class ProjectRoleRestrictionCacheDecorator
 *
 * @package Kanboard\Decorator
 * @author  Frederic Guillot
 */
class ProjectRoleRestrictionCacheDecorator
{
    protected $cachePrefix = 'project_restriction:';

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var ProjectRoleRestrictionModel
     */
    protected $projectRoleRestrictionModel;

    /**
     * ColumnMoveRestrictionDecorator constructor.
     *
     * @param CacheInterface                  $cache
     * @param ProjectRoleRestrictionModel     $projectRoleRestrictionModel
     */
    public function __construct(CacheInterface $cache, ProjectRoleRestrictionModel $projectRoleRestrictionModel)
    {
        $this->cache = $cache;
        $this->projectRoleRestrictionModel = $projectRoleRestrictionModel;
    }

    /**
     * Proxy method to get sortable columns
     *
     * @param  int    $project_id
     * @param  string $role
     * @return array|mixed
     */
    public function getAllByRole($project_id, $role)
    {
        $key = $this->cachePrefix.$project_id.$role;
        $projectRestrictions = $this->cache->get($key);

        if ($projectRestrictions === null) {
            $projectRestrictions = $this->projectRoleRestrictionModel->getAllByRole($project_id, $role);
            $this->cache->set($key, $projectRestrictions);
        }

        return $projectRestrictions;
    }
}
