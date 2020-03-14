<?php


namespace LoyaltyGroup\LoyaltyPoints\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class User extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('customer_entity', 'entity_id');
    }
}
