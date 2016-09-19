<?php

namespace Kanboard\Decorator;

use Kanboard\Core\Cache\CacheInterface;
use Kanboard\Model\ColumnMoveRestrictionModel;

/**
 * Class ColumnMoveRestrictionCacheDecorator
 *
 * @package Kanboard\Decorator
 * @author  Frederic Guillot
 */
class ColumnMoveRestrictionCacheDecorator
{
    protected $cachePrefix = 'column_move_restriction:';

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var ColumnMoveRestrictionModel
     */
    protected $columnMoveRestrictionModel;

    /**
     * ColumnMoveRestrictionDecorator constructor.
     *
     * @param CacheInterface             $cache
     * @param ColumnMoveRestrictionModel $columnMoveRestrictionModel
     */
    public function __construct(CacheInterface $cache, ColumnMoveRestrictionModel $columnMoveRestrictionModel)
    {
        $this->cache = $cache;
        $this->columnMoveRestrictionModel = $columnMoveRestrictionModel;
    }

    /**
     * Proxy method to get sortable columns
     *
     * @param  int    $project_id
     * @param  string $role
     * @return array|mixed
     */
    public function getSortableColumns($project_id, $role)
    {
        $key = $this->cachePrefix.$project_id.$role;
        $columnIds = $this->cache->get($key);

        if ($columnIds === null) {
            $columnIds = $this->columnMoveRestrictionModel->getSortableColumns($project_id, $role);
            $this->cache->set($key, $columnIds);
        }

        return $columnIds;
    }
}
