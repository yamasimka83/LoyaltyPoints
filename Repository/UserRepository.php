<?php

namespace LoyaltyGroup\LoyaltyPoints\Repository;

use LoyaltyGroup\LoyaltyPoints\Api\Model\UserInterface;
use LoyaltyGroup\LoyaltyPoints\Api\Repository\UserRepositoryInterface;
use LoyaltyGroup\LoyaltyPoints\Model\ResourceModel\User as ResourceModel;
use LoyaltyGroup\LoyaltyPoints\Model\UserFactory as ModelFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    private $resource;

    /**
     * @var ModelFactory
     */
    private $modelFactory;

    /**
     * UserRepository constructor.
     *
     * @param ResourceModel $resource
     * @param ModelFactory $modeFactory
     */
    public function __construct(
        ResourceModel $resource,
        ModelFactory $modeFactory
    ) {
        $this->resource             = $resource;
        $this->modelFactory         = $modeFactory;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): UserInterface
    {
        $user = $this->modelFactory->create();

        $this->resource->load($user, $id);

        if (empty($user->getId())) {
            throw new NoSuchEntityException(__("User %1 not found", $id));
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function save(UserInterface $user): UserInterface
    {
        try {
            $this->resource->save($user);
        } catch (\Exception $e) {
            /** TODO:  added logger */
            throw new CouldNotSaveException(__("User could not save"));
        }

        return $user;
    }
}
