<?php

namespace LoyaltyGroup\LoyaltyPoints\Repository;

use LoyaltyGroup\LoyaltyPoints\Api\Model\UserInterface;
use LoyaltyGroup\LoyaltyPoints\Api\Repository\UserRepositoryInterface;
use LoyaltyGroup\LoyaltyPoints\Model\ResourceModel\User as ResourceModel;
use LoyaltyGroup\LoyaltyPoints\Model\ResourceModel\User\Collection;
use LoyaltyGroup\LoyaltyPoints\Model\ResourceModel\User\CollectionFactory;
use LoyaltyGroup\LoyaltyPoints\Model\UserFactory as ModelFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
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
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $processor;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    public function __construct(
        ResourceModel $resource,
        ModelFactory $modeFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->resource             = $resource;
        $this->modelFactory         = $modeFactory;
        $this->collectionFactory    = $collectionFactory;
        $this->processor            = $collectionProcessor;
        $this->searchResultFactory  = $searchResultFactory;
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
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->processor->process($searchCriteria, $collection);

        /** @var SearchResultsInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();

        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setItems($collection->getItems());

        return $searchResult;
    }

    /**
     * @inheritDoc
     */
    public function save(UserInterface $user): UserInterface
    {
        try {
            $this->resource->save($user);
        } catch (\Exception $e) {
            // added logger
            throw new CouldNotSaveException(__("User could not save"));
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function delete(UserInterface $user): UserRepositoryInterface
    {
        try {
            $this->resource->delete($user);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException("User not delete");
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id): UserRepositoryInterface
    {
        $user = $this->getById($id);
        $this->delete($user);

        return $this;
    }
}
