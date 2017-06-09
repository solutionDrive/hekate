<?php
/**
 * Created by solutionDrive GmbH
 *
 * @author    Matthias Alt <alt@solutiondrive.de>
 * @date      01.06.17
 * @time:     15:57
 * @copyright 2017 solutionDrive GmbH
 */

use sd\hekate\lib\BitbucketRepositoryList;
use PHPUnit\Framework\TestCase;

class BitbucketRepositoryListTest extends TestCase
{
    /** @var  BitbucketRepositoryList */
    protected $testSubject;
    /** @var  \Bitbucket\API\Repositories */
    protected $repositoriesProphet;

    /** @var  \sd\hekate\lib\HekateCache */
    protected $cacheProphet;

    protected function setUp()
    {
        $bitbucketApiRepositoriesProphet = $this->prophesize(\Bitbucket\API\Repositories::class);

        $this->repositoriesProphet = $bitbucketApiRepositoriesProphet;
        $this->cacheProphet = $this->prophesize(\sd\hekate\lib\HekateCache::class);
        $this->testSubject = new BitbucketRepositoryList($this->repositoriesProphet->reveal(), $this->cacheProphet->reveal());
    }

    public function testSetCredentials()
    {
        $this->testSubject->setCredentials('my_user_name', 'my_password');
        $this->repositoriesProphet->setCredentials(\Prophecy\Argument::any())->shouldBeCalled();
    }

    public function testCreatePager()
    {
        $account = 'some_bitbucket_account';

        /** @var \Buzz\Message\Response $responseProphet */
        $responseProphet = $this->prophesize(\Buzz\Message\Response::class);
        $responseProphet->isOk()->willReturn(true);
        $response = $responseProphet->reveal();
        $this->repositoriesProphet
            ->all($account)
            ->shouldBeCalled()
            ->willReturn($response);
        $this->repositoriesProphet->getClient()->shouldBeCalled()->willReturn($this->prophesize(\Bitbucket\API\Http\Client::class)->reveal());

        $this->testSubject->createPager($account);
    }

    public function testGetAll()
    {

        $account = 'some_bitbucket_account';
        $repository1 = $this->_stubRepositoryInformation('name_of_repository1', 'project_of_repository1', 'slug_of_repository1');
        $repository2 = $this->_stubRepositoryInformation('name_of_repository2', 'project_of_repository2', 'slug_of_repository2');
        $this->prepareResponseProphet($repository1, $repository2, $account);

        $expected = [
            $repository1->name => [
                'name' => $repository1->name,
                'project' => $repository1->project->name,
                'slug'  => $repository1->slug
            ],
            $repository2->name => [
                'name' => $repository2->name,
                'project' => $repository2->project->name,
                'slug'  => $repository2->slug
            ]
        ];

        $cacheItemProphet = $this->prophesize('CacheItem')->willImplement(\Psr\Cache\CacheItemInterface::class);
        $cacheItemProphet->isHit()->willReturn(true);
        $cacheItemProphet->get()->willReturn($expected);


        $this->cacheProphet->getItem('full_list')->willReturn($cacheItemProphet->reveal());

        self::assertEquals($expected, $this->testSubject->getAll());
    }

    /**
     * @return stdClass
     */
    protected function _stubRepositoryInformation($nameOfRepository, $projectOfRepository, $slugOfRepository): stdClass
    {
        $repository = new stdClass();
        $repository->name = $nameOfRepository;
        $repository->project = new stdClass();
        $repository->project->name = $projectOfRepository;
        $repository->slug = $slugOfRepository;
        return $repository;
    }

    /**
     * @param $repository1
     * @param $repository2
     * @param $account
     */
    protected function prepareResponseProphet($repository1, $repository2, $account)
    {
        /** @var \Buzz\Message\Response $responseProphet */
        $responseProphet = $this->prophesize(\Buzz\Message\Response::class);
        $responseResult = [];
        $responseResult['values'] = [$repository1, $repository2];

        $responseProphet->isOk()->willReturn(true);
        $responseProphet->getContent()->willReturn(json_encode($responseResult));
        $response = $responseProphet->reveal();

        $this->repositoriesProphet
            ->all($account)
            ->shouldBeCalled()
            ->willReturn($response);
        $this->repositoriesProphet->getClient()->shouldBeCalled()->willReturn($this->prophesize(\Bitbucket\API\Http\Client::class)->reveal());

        $this->testSubject->createPager($account);
    }
}
