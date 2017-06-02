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

    protected function setUp()
    {
        $bitbucketApiRepositoriesProphet = $this->prophesize(\Bitbucket\API\Repositories::class);

        $this->repositoriesProphet = $bitbucketApiRepositoriesProphet;
        $this->testSubject = new BitbucketRepositoryList($this->repositoriesProphet->reveal());
    }

    public function testSetCredentials()
    {
        $this->testSubject->setCredentials('my_user_name', 'my_password');
        $this->repositoriesProphet->setCredentials(\Prophecy\Argument::any())->shouldBeCalled();
    }

    public function testCreatePager()
    {
        $account = 'some_bitbucket_account';
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
}
