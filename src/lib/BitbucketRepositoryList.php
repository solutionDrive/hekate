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

    /**
     * Creates a Pager Object for all Repositories of the given account
     * @param string $account
     */
    public function createPager($account)
    {
        $this->pager = new Pager($this->repositories->getClient(), $this->repositories->all($account));
    }

    /**
     * Gathers Information about all Repositories and returns it as Array
     * @return array
     */
    public function getAll()
    {
        $allRepositories = [];
        do {
            $responseForPage = $this->pager->getCurrent();
            $reposOfPage = json_decode($responseForPage->getContent());
            foreach ($reposOfPage->values as $repository) {
                $allRepositories = $this->_addRepositoryInformation($repository, $allRepositories);
            }
            $this->pager->fetchNext();
        } while ($this->pager->hasNext());
        return $allRepositories;
    }

    /**
     * @param string $projectKey
     * @return array
     */
    public function getAllForProjectKey($projectKey)
    {
        $aAllRepositories = $this->getAll();
        foreach ($aAllRepositories as $repository) {
            if ($repository['project'] !== $projectKey) {
                unset ($aAllRepositories[$repository['name']]);
            }
        }
        return $aAllRepositories;
    }

    /**
     * @param $repository
     * @param $allRepositories
     * @return mixed
     */
    protected function _addRepositoryInformation($repository, $allRepositories)
    {
        $allRepositories[$repository->name]['name'] = $repository->name;
        if ($this->_repositoryHasProjectInformation($repository)) {
            $allRepositories = $this->_addProjectInformationOfRepository($repository, $allRepositories);
        }
        if ($this->_repositoryHasSlug($repository)) {
            $allRepositories = $this->_addSlugInformationOfRepository($repository, $allRepositories);
        }
        return $allRepositories;
    }

    /**
     * @param $repo
     * @return bool
     */
    protected function _repositoryHasProjectInformation($repo): bool
    {
        return empty($repo->project) === false;
    }

    /**
     * @param $repository
     * @param $allRepositories
     * @return mixed
     */
    protected function _addProjectInformationOfRepository($repository, $allRepositories)
    {
        $allRepositories[$repository->name]['project'] = $repository->project->name;
        return $allRepositories;
    }

    /**
     * @param $repository
     * @return bool
     */
    protected function _repositoryHasSlug($repository): bool
    {
        return empty($repository->slug) === false;
    }

    /**
     * @param $repository
     * @param $allRepositories
     * @return mixed
     */
    protected function _addSlugInformationOfRepository($repository, $allRepositories)
    {
        $allRepositories[$repository->name]['slug'] = $repository->slug;
        return $allRepositories;
    }
}
