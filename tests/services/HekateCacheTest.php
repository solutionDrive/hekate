<?php
use PHPUnit\Framework\TestCase;
use sd\hekate\lib\HekateCache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

/**
 * Created by solutionDrive GmbH
 *
 * @author    Matthias Alt <alt@solutiondrive.de>
 * @date      09.06.17
 * @time:     20:24
 * @copyright 2017 solutionDrive GmbH
 */
class HekateCacheTest extends TestCase
{
    /** @var  FilesystemAdapter */
    protected $filesystemAdapterProphet;
    /** @var  HekateCache */
    protected $testSubject;

    protected function setUp()
    {
        $this->filesystemAdapterProphet = $this->prophesize(FilesystemAdapter::class);
        $this->testSubject = new HekateCache($this->filesystemAdapterProphet->reveal());
        parent::setUp();
    }

    public function testHasFileSystemAdapter()
    {
        self::assertAttributeInstanceOf(FilesystemAdapter::class, 'cache',$this->testSubject);
    }

    public function testDelegateGetItem()
    {
        $cacheKey = "some_key";
        $cacheValueProphet = $this->getCachItemProphet();
        $cacheValue = $cacheValueProphet->reveal();
        $this->filesystemAdapterProphet->getItem($cacheKey)->shouldBeCalled()->willReturn($cacheValue);
        self::assertEquals($cacheValue, $this->testSubject->getItem($cacheKey));
    }

    public function testDelegateGetItems()
    {
        $cacheKeys = [
            'key1',
            'key2'
        ];

        $cacheItemProphet1 = $this->getCachItemProphet();
        $cacheItemProphet2 = $this->getCachItemProphet();

        $expected = [
            'key1'  => $cacheItemProphet1->reveal(),
            'key2'  => $cacheItemProphet2->reveal()
        ];

        $this->filesystemAdapterProphet
            ->getItems($cacheKeys)
            ->willReturn($expected)
            ->shouldBeCalled();

        self::assertEquals($expected, $this->testSubject->getItems($cacheKeys));
    }

    public function testDelegateSaveItem()
    {
        $cacheKey = "some_chache_key";

        $cachitemProphet = $this->getCachItemProphet();

        /** @var CacheItem $cachitem */
        $cachitem = $cachitemProphet->reveal();
        $this->filesystemAdapterProphet->getItem($cacheKey)->willReturn($cachitem);

        $this->filesystemAdapterProphet->save($cachitem)->shouldBeCalled();

        $this->testSubject->saveItem($cachitem);

        self::assertEquals($cachitem, $this->testSubject->getItem($cacheKey));
    }
    /**
     * @return Object | Prophecy\Prophecy\ObjectProphecy
     */
    protected function getCachItemProphet()
    {
        /** @var CacheItem|Prophecy\Prophecy\ObjectProphecy $cacheItemProphet */
        $cacheItemProphet = $this->prophesize('CacheItem')->willImplement(\Psr\Cache\CacheItemInterface::class);

        return $cacheItemProphet;
    }
}