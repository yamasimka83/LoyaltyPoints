<?php

namespace LoyaltyGroup\LoyaltyPoints\Model;

use LoyaltyGroup\LoyaltyPoints\Api\Model\UserInterface;
use Magento\Framework\Model\AbstractModel;
use LoyaltyGroup\LoyaltyPoints\Model\ResourceModel\User as ResourceModel;

class User extends AbstractModel implements UserInterface
{

    public function _construct()
    {
        $this->_init(ResourceModel::class);
    }
    /**
     * @return mixed
     */
    public function getPoints()
    {
        return $this->getData('points');
    }

    /**
     * @param $points
     */
    public function setPoints($points)
    {
        $this->setData('points', $points);
    }
}

