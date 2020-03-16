<?php

namespace LoyaltyGroup\LoyaltyPoints\Api\Repository;

use LoyaltyGroup\LoyaltyPoints\Api\Model\UserInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface UserRepositoryInterface
{
    /**
     * Get user by ID.
     *
     * @param int $id
     * @throws NoSuchEntityException
     * @return UserInterface
     */
    public function getById(int $id) : UserInterface;

    /**
     * Save user to database.
     *
     * @param UserInterface $user
     * @throws CouldNotSaveException
     * @return UserInterface
     */
    public function save(UserInterface $user) : UserInterface;
}
