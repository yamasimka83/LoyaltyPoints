<?php


namespace LoyaltyGroup\LoyaltyPoints\Model\ResourceModel\User;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

use LoyaltyGroup\LoyaltyPoints\Model\User as Model;
use LoyaltyGroup\LoyaltyPoints\Model\ResourceModel\User as ResourceModel;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
