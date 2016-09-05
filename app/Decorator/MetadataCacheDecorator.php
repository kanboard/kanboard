<?php

namespace Kanboard\Decorator;

use Kanboard\Core\Cache\CacheInterface;
use Kanboard\Model\MetadataModel;

/**
 * Class MetadataCacheDecorator
 *
 * @package Kanboard\Decorator
 * @author  Frederic Guillot
 */
class MetadataCacheDecorator
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var MetadataModel
     */
    protected $metadataModel;

    /**
     * @var string
     */
    protected $cachePrefix;

    /**
     * @var int
     */
    protected $entityId;

    /**
     * Constructor
     *
     * @param CacheInterface     $cache
     * @param MetadataModel      $metadataModel
     * @param string             $cachePrefix
     * @param integer            $entityId
     */
    public function __construct(CacheInterface $cache, MetadataModel $metadataModel, $cachePrefix, $entityId)
    {
        $this->cache = $cache;
        $this->metadataModel = $metadataModel;
        $this->cachePrefix = $cachePrefix;
        $this->entityId = $entityId;
    }

    /**
     * Get metadata value by key
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default)
    {
        $metadata = $this->cache->get($this->getCacheKey());

        if ($metadata === null) {
            $metadata = $this->metadataModel->getAll($this->entityId);
            $this->cache->set($this->getCacheKey(), $metadata);
        }

        return isset($metadata[$key]) ? $metadata[$key] : $default;
    }

    /**
     * Set new metadata value
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->metadataModel->save($this->entityId, array(
            $key => $value,
        ));

        $metadata = $this->metadataModel->getAll($this->entityId);
        $this->cache->set($this->getCacheKey(), $metadata);
    }

    /**
     * Get cache key
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return $this->cachePrefix.$this->entityId;
    }
}
