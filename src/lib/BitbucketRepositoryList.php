<?php
/**
 * Created by solutionDrive GmbH
 *
 * @author    Matthias Alt <alt@solutiondrive.de>
 * @date      01.06.17
 * @time:     16:53
 * @copyright 2017 solutionDrive GmbH
 */

namespace sd\hekate\lib;

use Bitbucket\API\Authentication\Basic;
use Bitbucket\API\Http\Response\Pager;
use Bitbucket\API\Repositories;
use Bitbucket\API\Teams;

/**
 * Class BitbucketRepositoryList
 * @package sd\hekate\lib
 */
class BitbucketRepositoryList
{


    /** @var  string */
    protected $password;

    /** @var  string */
    protected $username;

    /** @var  Pager */
    protected $pager;

    /**
     * BitbucketRepositoryList constructor.
     */
    public function __construct(Repositories $repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * @param string $username
     * @param string $password
     */
    public function setCredentials($username, $password)
    {
        $this->repositories->setCredentials(new Basic($username, $password));
    }

    public function getAll()
    {
        $allRepositories = [];
        do {
            $responseForPage = $this->pager->getCurrent();
            $reposOfPage = json_decode($responseForPage->getContent());
            foreach ($reposOfPage->values as $repo) {
                $allRepositories[$repo->name]['name'] = $repo->name;
            }
            $this->pager->fetchNext();
        } while ($this->pager->hasNext());
        return $allRepositories;
    }

    /**
     * Creates a Pager Object for all Repositories of the given account
     * @param string $account
     */
    public function createPager($account)
    {
        $this->pager = new Pager($this->repositories->getClient(), $this->repositories->all($account));
    }
}
