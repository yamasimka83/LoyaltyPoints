<?php

namespace LoyaltyGroup\LoyaltyPoints\Model;

use LoyaltyGroup\LoyaltyPoints\Api\Model\UserInterface;
use LoyaltyGroup\LoyaltyPoints\Model\ResourceModel\User as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class User extends AbstractModel implements UserInterface
{

    public function _construct()
    {
        $this->_init(ResourceModel::class);
    }
    /**
     * @inheritDoc
     */
    public function getPoints()
    {
        return $this->getData('points');
    }

    /**
     * @inheritDoc
     */
    public function setPoints($points)
    {
        $this->setData('points', $points);
    }
}

