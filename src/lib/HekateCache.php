<?php
/**
 * Created by solutionDrive GmbH
 *
 * @author    Matthias Alt <alt@solutiondrive.de>
 * @date      09.06.17
 * @time:     20:26
 * @copyright 2017 solutionDrive GmbH
 */

namespace sd\hekate\lib;


use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Class HekateCache
 * @package sd\hekate\lib
 */
class HekateCache
{
    /** @var FilesystemAdapter  */
    protected $cache;

    /**
     * HekateCache constructor.
     * @param FilesystemAdapter $cache
     */
    public function __construct(FilesystemAdapter $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param $key
     * @return mixed|\Symfony\Component\Cache\CacheItem
     */
    public function getItem($key)
    {
        return $this->cache->getItem($key);
    }

    /**
     * @param $cacheKeys
     * @return array|\Generator|\Traversable
     */
    public function getItems($cacheKeys)
    {
        return $this->cache->getItems($cacheKeys);
    }

    /**
     * @param CacheItemInterface $cacheItem
     */
    public function saveItem(CacheItemInterface $cacheItem)
    {
        $this->cache->save($cacheItem);
    }
}
