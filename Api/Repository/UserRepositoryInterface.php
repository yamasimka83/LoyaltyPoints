<?php

namespace LoyaltyGroup\LoyaltyPoints\Api\Repository;

use LoyaltyGroup\LoyaltyPoints\Api\Model\UserInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface UserRepositoryInterface
{
    /**
     * Get user by ID
     *
     * @param int $id
     * @throws NoSuchEntityException
     * @return UserInterface
     */
    public function getById(int $id) : UserInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria) : SearchResultsInterface;

    /**
     * @param UserInterface $user
     * @throws CouldNotSaveException
     * @return UserInterface
     */
    public function save(UserInterface $user) : UserInterface;

    /**
     * @param UserInterface $user
     * @throws CouldNotDeleteException
     * @return UserRepositoryInterface
     */
    public function delete(UserInterface $user) : UserRepositoryInterface;

    /**
     * @param int $id
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     * @return UserRepositoryInterface
     */
    public function deleteById(int $id) : UserRepositoryInterface;
}
