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
    protected $repositoriesProphte;

    protected function setUp()
    {
        $bitbucketApiRepositoriesProphet = $this->prophesize(\Bitbucket\API\Repositories::class);

        $this->repositoriesProphte = $bitbucketApiRepositoriesProphet;
        $this->testSubject = new BitbucketRepositoryList($this->repositoriesProphte->reveal());
    }

    public function testSetCredentials()
    {
        $this->testSubject->setCredentials('my_user_name', 'my_password');
        $this->repositoriesProphte->setCredentials(\Prophecy\Argument::any())->shouldBeCalled();
    }
}
